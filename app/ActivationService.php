<?php
namespace App;

use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Repositories\ActivationRepository;

class ActivationService
{
    /**
     * Mailer provider
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * Activation repository
     *
     * @var ActivationRepository
     */
    protected $activationRepo;

    /**
     * Resend flag
     *
     * @var int
     */
    protected $resendAfter = 24;

    /**
     * The constructor of ActivationService
     *
     * @param Mailer $mailer
     * @param ActivationRepository $activationRepo
     */
    public function __construct(Mailer $mailer, ActivationRepository $activationRepo)
    {
        $this->mailer = $mailer;
        $this->activationRepo = $activationRepo;
    }

    /**
     * Sends the activation email to the user
     *
     * @param User $user
     */
    public function sendActivationMail($user)
    {

        if ($user->active || !$this->shouldSend($user)) {
            return;
        }

        $token = $this->activationRepo->createActivation($user);

        $link = route('user.activate', $token);
        $message = sprintf('Activate account <a href="%s">%s</a>', $link, $link);

        $this->mailer->raw($message, function (Message $m) use ($user) {
            $m->to($user->email)->subject('Activation mail');
        });
    }

    /**
     * Activates the user
     *
     * @param User $user
     */
    public function activateUser($token)
    {
        $activation = $this->activationRepo->getActivationByToken($token);

        if ($activation === null) {
            return null;
        }

        $user = User::find($activation->user_id);
        $user->active = true;
        $user->save();
        $this->activationRepo->deleteActivation($token);
        return $user;

    }

    /**
     * Check whether the activation email should or not be sent
     *
     * @param User $user
     */
    private function shouldSend($user)
    {
        $activation = $this->activationRepo->getActivation($user);
        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }
}
