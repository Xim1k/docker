<?php

namespace App\Service;


use App\Entity\Seller;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class AuthService
{
    private $mailer;
    private $templating;
    private $generator;
    private $manager;
    private $passwordEncoder;

    public function __construct(\Swift_Mailer $mailer, Environment $templating, RouterInterface $generator,
                                ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->generator = $generator;
        $this->manager = $manager;
        $this->passwordEncoder = $encoder;
    }

    public function generate()
    {
        $token = time() . '_' . uniqid("", TRUE);

        return $token;
    }

    public function register(User $user, string $domen, string $plainPassword, string $template, $title)
    {
        $repository = $this->manager->getRepository(User::class);
        while (1) {
            $token = $this->generate();
            $userCheck = $repository->getUserByToken($token);
            if (!$userCheck) {
                $user->setToken($token);
                break;
            }
        }
        // encode the plain password
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $plainPassword
            )
        );

        $this->manager->persist($user);
        $this->manager->flush();


        $this->sendEmail($domen, $user, $template, $plainPassword, $title);
    }

    public function sendEmail(string $domen, User $user, $template, $plainPassword, $title)
    {
        $url = $domen . $this->generator->generate('activation', ['token' => $user->getToken()]);

        $message = (new \Swift_Message($title))
            ->setFrom('delivery.dev@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    $template,
                    [
                        'name' => $user->getLogin(),
                        'token' => $url,
                        'password' => $plainPassword
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

    public function generateStr()
    {
        $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
        $max = 10;
        $size = strlen($chars) - 1;
        $str= null;
        while ($max--)
            $str .= $chars[rand(0, $size)];

        return $str;
    }

    public function forgotPasswordService(User $user)
    {
        $plainPassword = $this->generateStr();
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $plainPassword
            )
        );

        $this->manager->persist($user);
        $this->manager->flush();

        $message = (new \Swift_Message('Восстановление пароля'))
            ->setFrom('delivery.dev@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'email/reset_password.html.twig',
                    [
                        'name' => $user->getLogin(),
                        'password' => $plainPassword,
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);

    }

}