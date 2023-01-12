<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;
use Config;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\BirthdayNotificationCron::class,
        Commands\MissingTopicNotificationCron::class,
        Commands\TopicPreviewNotificationCron::class,
       // Commands\TopicReminderNotificationCron::class,
        Commands\RemoveMemberNotificationCron::class,
        Commands\AppInActivityReminderCron::class,
        Commands\UpdateTrialMemberAdmissionStatusCron::class,
        Commands\TrailExpiryReminderNotification::class,
        Commands\TrailExpiryReminderSendEmail::class,

        Commands\MemberModuleCertificateEligibility::class,
        Commands\MemberModuleCertificateGenerate::class,
        Commands\MemberModuleCertificateExpired::class,
        Commands\ModuleCertificateEmailSendToMember::class,
        Commands\MemberCustomNotificationProcess::class,
        Commands\DailyInfo::class,
        Commands\UpdateSearchKeywordCron::class,
        Commands\AutoAssignMemberToGroupCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $todays_date = date("Y-m-d H:i:s");
        \Log::info("Master Cron: ".$todays_date);
        $cron_schedules_path = public_path() . "/cron/schedules.json";
        $cron_schedules_json = json_decode(file_get_contents($cron_schedules_path), true); 
        $schedule_data = Config::get('constants.CRON_AVAILABLE_SCHEDULES');
        if(count($cron_schedules_json) > 0){
            foreach($cron_schedules_json as $scheduled_crons){
                if($scheduled_crons['status'] == 1){
                    $schedule_command   = $scheduled_crons['command'];
                    $schedule_code      = $scheduled_crons['code'];
                    $schedule_format    = $scheduled_crons['schedule_format'];
                    $schedule_timing    = $scheduled_crons['schedule_timing'];
                    $schedule_timing_arr= array();

                    if(trim($schedule_timing) != ""){
                        $schedule_timing_arr = json_decode($schedule_timing, true); 
                    }
                    $schedule->command($schedule_command)->everyMinute(); //Only on local for testing
                    if(isset($schedule_data[$schedule_format])){
                        
                        $schedule_row_data = $schedule_data[$schedule_format];

                        if($schedule_row_data["method"] == "everyMinute"){ // "schedule_description": "Run the task every minute",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "everyFiveMinutes"){ // "schedule_description": "Run the task every five minutes",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "everyTenMinutes"){ // "schedule_description": "Run the task every ten minutes",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "everyFifteenMinutes"){ // "schedule_description": "Run the task every fifteen minutes",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "everyThirtyMinutes"){ // "schedule_description": "Run the task every Thirty minutes",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "hourly"){ // "schedule_description": "Run the task every hour",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "hourlyAt"){ // "schedule_description": "Run the task every hour at added mins past the hour",
                            $minutes = $schedule_timing_arr["minutes"];
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}($minutes);
                        }else if($schedule_row_data["method"] == "daily"){ // "schedule_description": "Run the task every day at midnight",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "dailyAt"){ // "schedule_description": "Run the task every day at Added Hours",
                            $hours = $schedule_timing_arr["hours"];
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}($hours);
                        }else if($schedule_row_data["method"] == "twiceDaily"){ // "schedule_description": "Run the task daily at 1:00 & 13:00",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "weekly"){ // "schedule_description": "Run the task every week",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "weeklyOn"){ // "schedule_description": "Run the task every week on Tuesday at 8:00 If(week_day=1 and hours=8:00)",
                            $week_day = $schedule_timing_arr["week_day"];
                            $hours = $schedule_timing_arr["hours"];
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}($week_day, $hours);
                        }else if($schedule_row_data["method"] == "monthly"){ //"schedule_description": "Run the task every month",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "monthlyOn"){ // "schedule_description": "Run the task every month on the 4th at 15:00 If(day=4 and Hours=15)",
                             $day = $schedule_timing_arr["day"];
                            $hours = $schedule_timing_arr["hours"];
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}($day, $hours);
                        }else if($schedule_row_data["method"] == "quarterly"){ // "schedule_description": "Run the task every quarter",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else if($schedule_row_data["method"] == "yearly"){ // "schedule_description": "Run the task every year",
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }else{
                            $schedule->command($schedule_command)->{$schedule_row_data["method"]}();
                        }
                    }
                }
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
