<?php

namespace App\Console\Commands;

use App\Mail\Email;
use App\Models\MailWorker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mailWorker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This shall serve as a test. Add to logs every minute';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $mws = MailWorker::where('sent_at', '=', null)->limit(20)->get();

        foreach($mws as $mw) {
            $mw->data = json_decode($mw->data);
            try {

                Mail::to($mw->recipient)->send(new Email($mw));

                $mw->data = json_encode($mw->data);
                $mw->sent_at = now();
                $mw->save();

            } catch (\Exception $ex) {
                Log::info($ex);
            }
        }
    }
}
