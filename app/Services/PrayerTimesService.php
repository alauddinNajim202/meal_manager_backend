<?php
namespace App\Services;

class PrayerTimesService
{
    private $lat;
    private $lng;
    private $timezone;
    private $date;

    const JAFARI  = 0;
    const KARACHI = 1;
    const ISNA    = 2;
    const MWL     = 3;
    const MAKKAH  = 4;
    const EGYPT   = 5;

    private $calcMethod  = self::MWL;
    private $asrJuristic = 0; // 0 = Shafii, 1 = Hanafi

    private $methodParams = [
        self::JAFARI  => [16, 0, 4, 0, 14],
        self::KARACHI => [18, 1, 0, 0, 18],
        self::ISNA    => [15, 1, 0, 0, 15],
        self::MWL     => [18, 1, 0, 0, 17],
        self::MAKKAH  => [18.5, 1, 0, 1, 90],
        self::EGYPT   => [19.5, 1, 0, 0, 17.5],
    ];

    public function __construct($lat, $lng, $timezone, $date = null, $method = self::MWL)
    {
        $this->lat        = $lat;
        $this->lng        = $lng;
        $this->timezone   = $timezone;
        $this->date       = $date ?? date('Y-m-d');
        $this->calcMethod = $method;
    }

    public function getPrayerTimes()
    {
        $timestamp = strtotime($this->date);
        $year      = date('Y', $timestamp);
        $month     = date('m', $timestamp);
        $day       = date('d', $timestamp);

        $julianDate = $this->getJulianDate($year, $month, $day) - $this->lng / (15 * 24);

        return $this->computeDayTimes($julianDate);
    }

    private function getJulianDate($year, $month, $day)
    {
        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }

        $A = floor($year / 100);
        $B = 2 - $A + floor($A / 4);

        return floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $B - 1524.5;
    }

    private function computeDayTimes($julianDate)
    {
        $params = $this->methodParams[$this->calcMethod];

        $sunDeclination = $this->sunDeclination($julianDate);
        $eqTime         = $this->equationOfTime($julianDate);

        // Calculate Dhuhr (midday)
        $dhuhr = $this->computeMidDay($julianDate);

                                     // Calculate Sunrise & Sunset
        $sunriseSunsetAngle = 0.833; // Sunrise/sunset angle
        $sunPosition        = $this->sin($sunDeclination) * $this->sin($this->lat);
        $sunPosition2       = $this->cos($sunDeclination) * $this->cos($this->lat);

        $sunriseOffset = (1 / 15.0) * $this->arccos(
            (-$this->sin($sunriseSunsetAngle) - $sunPosition) / $sunPosition2
        );

        $sunrise = $dhuhr - $sunriseOffset;
        $sunset  = $dhuhr + $sunriseOffset;

        // Calculate Fajr
        $fajrAngle  = $params[0];
        $fajrOffset = (1 / 15.0) * $this->arccos(
            (-$this->sin($fajrAngle) - $sunPosition) / $sunPosition2
        );
        $fajr = $dhuhr - $fajrOffset;

        // Calculate Isha
        $ishaAngle  = $params[4];
        $ishaOffset = (1 / 15.0) * $this->arccos(
            (-$this->sin($ishaAngle) - $sunPosition) / $sunPosition2
        );
        $isha = $dhuhr + $ishaOffset;

        // Calculate Asr - FIXED CALCULATION
        $asr = $this->computeAsrTime($julianDate, $this->asrJuristic);

                                       // Calculate Maghrib (a few minutes after sunset)
        $maghrib = $sunset + 2 / 60.0; // 2 minutes after sunset

        $times = [
            'Fajr'    => $fajr,
            'Sunrise' => $sunrise,
            'Dhuhr'   => $dhuhr,
            'Asr'     => $asr,
            'Sunset'  => $sunset,
            'Maghrib' => $maghrib,
            'Isha'    => $isha,
        ];

        return $this->adjustTimes($times);
    }

    private function computeAsrTime($jd, $method)
    {
        $decl = $this->sunDeclination($jd);
        $noon = $this->computeMidDay($jd);

        // Shafii: shadow length = 1, Hanafi: shadow length = 2
        $shadowLength = $method + 1;

        $angle = $this->arccot($shadowLength + $this->tan(abs($this->lat - $decl)));

        // Ensure the angle is positive for calculation
        $angle = abs($angle);

        $asrOffset = (1 / 15.0) * $this->arccos(
            ($this->sin($angle) - $this->sin($decl) * $this->sin($this->lat)) /
            ($this->cos($decl) * $this->cos($this->lat))
        );

        return $noon + $asrOffset;
    }

    private function computeMidDay($jd)
    {
        $eqt = $this->equationOfTime($jd);
        return 12 - $eqt;
    }

    private function sunDeclination($jd)
    {
        $d = $jd - 2451545.0;
        $g = $this->fixAngle(357.529 + 0.98560028 * $d);
        $q = $this->fixAngle(280.459 + 0.98564736 * $d);
        $L = $this->fixAngle($q + 1.915 * $this->sin($g) + 0.020 * $this->sin(2 * $g));

        $e = 23.439 - 0.00000036 * $d;

        return $this->arcsin($this->sin($e) * $this->sin($L));
    }

    private function equationOfTime($jd)
    {
        $d = $jd - 2451545.0;
        $g = $this->fixAngle(357.529 + 0.98560028 * $d);
        $q = $this->fixAngle(280.459 + 0.98564736 * $d);
        $L = $this->fixAngle($q + 1.915 * $this->sin($g) + 0.020 * $this->sin(2 * $g));

        $e  = 23.439 - 0.00000036 * $d;
        $RA = $this->arctan2($this->cos($e) * $this->sin($L), $this->cos($L)) / 15.0;

        $eqt = $q / 15.0 - $this->fixHour($RA);
        return $eqt;
    }

    private function adjustTimes($times)
    {
        $formatted = [];

        foreach ($times as $key => $time) {
            // Adjust for timezone and longitude
            $time = $time + $this->timezone - $this->lng / 15.0;
            $time = $this->fixHour($time);

            $hours   = floor($time);
            $minutes = round(($time - $hours) * 60);

            // Handle minute overflow
            if ($minutes >= 60) {
                $hours += 1;
                $minutes -= 60;
            }
            if ($minutes < 0) {
                $hours -= 1;
                $minutes += 60;
            }

            // Handle hour overflow
            $hours = $hours % 24;
            if ($hours < 0) {
                $hours += 24;
            }

            $formatted[$key] = sprintf('%02d:%02d', $hours, $minutes);
        }

        return $formatted;
    }

    // Math helper functions
    private function sin($d)
    {return sin(deg2rad($d));}
    private function cos($d)
    {return cos(deg2rad($d));}
    private function tan($d)
    {return tan(deg2rad($d));}
    private function arcsin($x)
    {return rad2deg(asin($x));}
    private function arccos($x)
    {return rad2deg(acos($x));}
    private function arctan($x)
    {return rad2deg(atan($x));}
    private function arctan2($y, $x)
    {return rad2deg(atan2($y, $x));}
    private function arccot($x)
    {return 90 - $this->arctan($x);}

    private function fixAngle($angle)
    {
        $angle = fmod($angle, 360);
        return $angle < 0 ? $angle + 360 : $angle;
    }

    private function fixHour($hour)
    {
        $hour = fmod($hour, 24);
        return $hour < 0 ? $hour + 24 : $hour;
    }
}
