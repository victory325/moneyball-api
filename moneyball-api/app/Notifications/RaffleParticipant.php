<?php

namespace App\Notifications;

use App\Helpers\SettingHelper;
use App\Models\Raffle;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class RaffleParticipant extends Notification
{
    use Queueable;

    protected $raffle;

    /**
     * RaffleParticipant constructor.
     *
     * @param Raffle $raffle
     */
    public function __construct(Raffle $raffle)
    {
        $this->raffle = $raffle;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        $message = 'This week winner - %s. Prize was %s %s. Keep playing and maybe you are lucky next week!';

        return sprintf($message, $this->raffle->winnerName, $this->raffle->amount, SettingHelper::raffleCurrency());
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [OneSignalChannel::class];
    }

    /**
     * @param $notifiable
     *
     * @return OneSignalMessage
     */
    public function toOneSignal($notifiable): OneSignalMessage
    {
        return OneSignalMessage::create()
            ->subject('The winner of the raffle of this week is..')
            ->body($this->getMessage());
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
