<?php

namespace AppBundle\Command;

use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\Category;
use EntityBundle\Entity\Shop;
use EntityBundle\Entity\User;
use EntityBundle\Repository\CategoryRepository;
use EntityBundle\Repository\ShopRepository;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreatingEntitiesCommand
 * @package App\Command
 */
class CreatingEntitiesCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:create-entities';

    /**
     * @var ProducerInterface
     */
    protected $producer;

    /**
     * @var array
     */
    protected $curlHeaders;

    /**
     * @var mixed
     */
    protected $user;

    /**
     * @var array
     */
    protected $defaultTypes;

    /**
     * CreatingEntitiesCommand constructor.
     */
    public function __construct()
    {
        $this->defaultTypes = ['shop', 'feedback', 'product'];
        $this->curlHeaders  = [
            'accept: application/json',
            'Content-Type: application/json',
        ];

        $this->attachTheToken('admin', 'password1234');
        $this->user = $this->getMe();

        parent::__construct();
    }

    /**
     * Configuration command
     */
    protected function configure()
    {
        $this->setDescription('Creating entities through the api')
            ->setHelp('This command allows you to create entities')
            ->addArgument('entities-type', InputArgument::IS_ARRAY);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->producer = $this->getContainer()->get('old_sound_rabbit_mq.test_producer');
        $types          = $input->getArgument('entities-type') ?: $this->defaultTypes;

        if (!$this->user) {
            $this->producer->publish("\r\n Token is not received");

            return;
        }

        $responses = [];

        try {
            if (in_array('shop', $types)) {
                $responses['shop'] = $this->createShop();
            }

            if (in_array('feedback', $types)) {
                $responses['feedback'] = $this->createFeedback();
            }

            if (in_array('product', $types)) {
                $responses['product'] = $this->createProduct();
            }

            foreach ($responses as $entity => $response) {
                if (isset($response->id)) {
                    $this->producer->publish("\r\n {$entity} with id {$response->id} successful created");
                } else {
                    $this->producer->publish("\r\n {$entity} error - " . $response->detail);
                }
            }
        } catch (\Exception $exception) {
            $this->producer->publish("\r\n error - " . $exception->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function createShop()
    {
        $url = 'http://127.0.0.1:8001/api/shops';

        $shopData = [
            'name'        => 'shop ' . rand(100, 100000),
            'description' => md5(rand(0, 100)),
            'phoneNumber' => '7' . rand(100000000, 999999999),
            'lon'         => rand(30, 55) . '.' . rand(1000000, 9999999),
            'lat'         => rand(30, 55) . '.' . rand(1000000, 9999999),
        ];

        return $this->sendRequest($url, $shopData);
    }

    /**
     * @return mixed
     */
    public function createFeedback()
    {
        $url  = 'http://127.0.0.1:8001/api/feedback';

        $feedbackData = [
            'user'    => "/api/users/{$this->user->id}",
            'message' => md5(rand(2, 166)),
        ];

        return $this->sendRequest($url, $feedbackData);
    }

    /**
     * @return mixed
     */
    public function createProduct()
    {
        $url = 'http://127.0.0.1:8001/api/products';

        $productData = [
            'category'    => '/api/categories/77',
            'title'       => 'product' . mt_rand(1, 3000) . mt_rand(1, 5000),
            'price'       => mt_rand(1, 250),
            'rating'      => mt_rand(0, 9) . '.' . mt_rand(1, 99),
            'description' => md5(rand(5, 11111)),
            'number'      => rand(1, 32),
        ];

        return $this->sendRequest($url, $productData);
    }

    /**
     * @return mixed
     */
    public function getMe()
    {
        $url = 'http://127.0.0.1:8001/api/me';

        return $this->sendRequest($url, [], 'GET');
    }

    /**
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function getToken(string $username, string $password)
    {
        $url      = 'http://127.0.0.1:8001/authentication_token';
        $userData = ['email' => $username, 'password' => $password];

        return $this->sendRequest($url, $userData);
    }

    /**
     * @param string $username
     * @param string $password
     */
    public function attachTheToken(string $username, string $password)
    {
        $response = $this->getToken($username, $password);

        if (isset($response->token)) {
            $this->curlHeaders[] = 'Authorization: Bearer ' . $response->token;
        }
    }

    /**
     * @param string $url
     * @param array  $data
     * @param string $method
     * @return mixed
     */
    public function sendRequest(string $url, array $data = [], string $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->curlHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        return $response;
    }
}