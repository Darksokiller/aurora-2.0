<?php
namespace Application\Utilities;

use Laminas\Config\Config;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Message;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;
use Laminas\ServiceManager\ServiceLocatorInterface;
use User\Model\User as User;
use \RuntimeException;

class Mailer implements ResourceInterface
{

    const RESOURCE_ID = 'mailService';

    const VERIFICATION = 'verificationMessage';

    const WELCOME = 'welcomeMessage';

    const RESET_PASSWORD = 'resetPasswordMessage';

    const NEWSLETTER = 'newsletterMessage';

    /**
     *
     * @var $acl Acl
     */
    private $acl;

    /**
     *
     * @var $appSettings \Laminas\Config\Config
     */
    protected $appSettings;

    /**
     *
     * @var $request \Laminas\Http\PhpEnvironment\Request
     */
    protected $request;

    /**
     *
     * @var $hostName string|HTTP_HOST
     */
    protected $hostName;

    /**
     *
     * @var $requestScheme string|http https|REQUEST_SCHEME
     */
    protected $requestScheme;

    public $message;

    public $user;

    /**
     *
     * @var \Laminas\Mail\Transport\Smtp
     */
    protected $transport;

    public function __construct(Config $settings = null, Request $request = null, ServiceLocatorInterface $sm)
    {
        $this->debug = $sm->get('Application\Utilities\Debug');
        if (! empty($settings)) {
            $this->appSettings = $settings;
        }
        if (! empty($request)) {
            $this->request = $request;
            $this->hostName = $this->request->getServer('HTTP_HOST');
            $this->requestScheme = $this->request->getServer('REQUEST_SCHEME');
        }
        $transport = new SmtpTransport();

        $options = new SmtpOptions([
            'name' => 'webinertia.net',
            'host' => 'smtp-relay.gmail.com',
            'port' => '587',
            'connection_class' => 'login',
            'connection_config' => [
                'username' => $this->appSettings->smtpSenderAddress,
                'password' => $this->appSettings->smtpSenderPasswd,
                'ssl' => 'tls'
            ]
        ]);
        $transport->setOptions($options);
        // currently only Smtp transport is supported
        $this->setTransport($transport);
    }

    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    public function sendMessage($address, $type, $token = null)
    {
        try {
            $message = new Message();
            $message->addTo($address);
            // This email must match the connection_config key in the options above
            $message->addFrom($this->appSettings->smtpSenderAddress);
            $message->setSubject($this->appSettings->appName . ' account verification');
            switch ($type) {
                case self::VERIFICATION:
                    $this->verificationMessage($address, $message, $token);
                    break;
                case self::WELCOME:

                    break;
                case self::RESET_PASSWORD:

                    break;
                default:
                    throw new \RuntimeException('Unsupported message type detected!!');
                    break;
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }

    protected function verificationMessage($address, $message, $hash)
    {
        try {
            if (empty($hash)) {
                throw new \RuntimeException('You must pass a token to send a verification email!!');
            }
            $textContent = 'Please click the link below to verify your account.';

            $text = new MimePart($textContent);
            $text->type = Mime::TYPE_TEXT;
            $text->charset = 'utf-8';
            $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

            $htmlMarkup = '<p>' . $textContent . '<br>';
            $format = '<a href="%s://%s/user/register/verify?token=%s">Verify Account</a>';
            $htmlMarkup .= sprintf($format, $this->requestScheme, $this->hostName, $hash);
            $htmlMarkup .= '</p>';

            $html = new MimePart($htmlMarkup);
            $html->type = Mime::TYPE_HTML;
            $html->charset = 'utf-8';
            $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

            $body = new MimeMessage();
            $body->setParts([
                $text,
                $html
            ]);
            $message->setBody($body);
            $contentTypeHeader = $message->getHeaders()->get('Content-Type');
            $contentTypeHeader->setType('multipart/alternative');

            try {
                $this->transport->send($message);
            } catch (RuntimeException $e) {
                echo $e->getMessage();
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }
}