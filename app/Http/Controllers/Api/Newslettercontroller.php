<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatAuditLog;
use App\Models\NewsletterAudit;
use App\Models\NewsletterContent;
use App\Models\NewsletterDeliveryLog;
use Illuminate\Http\Request;

class Newslettercontroller extends Controller
{
    //

    // Store Newsletter Content
    public function store_content(Request $request)
    {
        $request->validate([
            'title'          => 'required|string',
            'summary'        => 'required|string',
            'category'       => 'required|string',
            'language'       => 'required|string',
            'version_id'     => 'required|string',

            'why_it_matters' => 'nullable|string',
        ]);

        $content                 = new NewsletterContent();
        $content->title          = $request->title;
        $content->summary        = $request->summary;
        $content->category       = $request->category;
        $content->language       = $request->language;
        $content->version_id     = $request->version_id;
        $content->approved_at    = $request->approved_at ?? null;
        $content->approved_by    = $request->approved_by ?? null;
        $content->why_it_matters = $request->why_it_matters ?? null;
        $content->save();

        return response()->json([
            'message' => 'Newsletter content saved successfully',
            'data'    => $content,
        ], 201);
    }

    // Store Newsletter Audit
    public function store_audit(Request $request)
    {
        $request->validate([
            'category'   => 'required|string',
            'language'   => 'required|string',
            'version_id' => 'required|string',
        ]);

        $audit              = new NewsletterAudit();
        $audit->category    = $request->category;
        $audit->language    = $request->language;
        $audit->version_id  = $request->version_id;
        $audit->approved_at = $request->approved_at ?? null;
        $audit->approved_by = $request->approved_by ?? null;
        $audit->save();

        return response()->json([
            'message' => 'Newsletter audit saved successfully',
            'data'    => $audit,
        ], 201);
    }

    // Store Newsletter Delivery Log
    public function store_delivery(Request $request)
    {
        $request->validate([
            'send_timestamp'  => 'required|string',
            'channel'         => 'required|string',
            'is_success'      => 'required|boolean',
            'recipient_count' => 'nullable|string',
        ]);

        $log                  = new NewsletterDeliveryLog();
        $log->send_timestamp  = $request->send_timestamp;
        $log->channel         = $request->channel;
        $log->is_success      = $request->is_success;
        $log->recipient_count = $request->recipient_count ?? null;
        $log->save();

        return response()->json([
            'message' => 'Newsletter delivery log saved successfully',
            'data'    => $log,
        ], 201);
    }

    public function chat_audit_store(Request $request)
    {
        $request->validate([
            'timestamp'            => 'required|string',
            'language'             => 'required|string',
            'category'             => 'required|string',
            'outcome'              => 'required|string',
            'reason_code'          => 'required|string',
            'agent_type'           => 'required|string',
            'policy_result'        => 'required|string',
            'question_fingerprint' => 'required|string',
        ]);

        // Database check: Check if a similar record already exists
        // $existingRecord = ChatAuditLog::where('timestamp', $request->timestamp)
        //     ->where('question_fingerprint', $request->question_fingerprint)
        //     ->where('category', $request->category)
        //     ->where('language', $request->language)
        //     ->first();

        // if ($existingRecord) {
        //     return response()->json([
        //         'message' => 'Chat audit log already exists',
        //         'data'    => $existingRecord,
        //         'status'  => 'duplicate'
        //     ], 409);
        // }

        // Create new chat audit log
        $auditLog                       = new ChatAuditLog();
        $auditLog->timestamp            = $request->timestamp;
        $auditLog->language             = $request->language;
        $auditLog->category             = $request->category;
        $auditLog->outcome              = $request->outcome;
        $auditLog->reason_code          = $request->reason_code;
        $auditLog->agent_type           = $request->agent_type;
        $auditLog->policy_result        = $request->policy_result;
        $auditLog->question_fingerprint = $request->question_fingerprint;
        $auditLog->save();

        return response()->json([
            'message' => 'Chat audit log saved successfully',
            'data'    => $auditLog,
            'status'  => 'created',
        ], 201);
    }

}
