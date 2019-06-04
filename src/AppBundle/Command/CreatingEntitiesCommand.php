<?php

namespace AppBundle\Command;

use Doctrine\ORM\EntityManager;
use EntityBundle\Entity\Category;
use EntityBundle\Entity\Shop;
use EntityBundle\Entity\User;
use EntityBundle\Repository\CategoryRepository;
use EntityBundle\Repository\ShopRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreatingEntitiesCommand
 * @package App\Command
 */
class CreatingEntitiesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:create-entities';

    /**
     * @var array
     */
    protected $curlHeaders;

    /** @var string */
    protected $token;

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

        /**
         * Get the JWT token and attach it to the headers
         */
        $url      = 'http://127.0.0.1:8001/authentication_token';
        $userData = ['email' => 'admin', 'password' => 'password1234'];

        $response = $this->sendRequest($url, $userData);

        if (isset($response->token)) {
            $token               = $response->token;
            $this->curlHeaders[] = 'Authorization: Bearer ' . $token;
            $this->token         = $token;
        }

        parent::__construct();
    }

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
        $types = $input->getArgument('entities-type') ?: $this->defaultTypes;

        if (empty($this->token)) {
            $output->writeln("\r\n Token is not received");
            return;
        }

        if (in_array('shop', $types)) {
            $response = $this->createShop();

            if (isset($response->id)) {
                $output->writeln("\r\n Shop with id {$response->id} successful created");
            } else {
                $output->writeln("\r\n Shop error - " . $response->detail);
            }
        }

        if (in_array('feedback', $types)) {
            $response = $this->createFeedback();

            if (isset($response->id)) {
                $output->writeln("\r\n Feedback with id {$response->id} successful created");
            } else {
                $output->writeln("\r\n Feedback error - " . $response->detail);
            }
        }

        if (in_array('product', $types)) {
            $response = $this->createProduct();

            if (isset($response->id)) {
                $output->writeln("\r\n Product with id {$response->id} successful created");
            } else {
                $output->writeln("\r\n Product error - " . $response->detail);
            }
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
        $user = $this->getMe();
        $url  = 'http://127.0.0.1:8001/api/feedback';

        $feedbackData = [
            'user'    => "/api/users/{$user->id}",
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
            'category'    => '/api/categories/51',
            'title'       => 'product' . mt_rand(1, 3000) . mt_rand(1, 5000),
            'price'       => mt_rand(1, 250),
            'rating'      => mt_rand(0, 9) . '.' . mt_rand(1, 99),
            'description' => md5(rand(5, 11111)),
            'number'      => rand(1, 32),
        ];

        return $this->sendRequest($url, $productData);
    }

    public function getMe()
    {
        $url = 'http://127.0.0.1:8001/api/me';

        return $this->sendRequest($url, [], 'GET');
    }

    /**
     * @param string $url
     * @param array  $data
     * @param string $method
     * @return mixed
     */
    public function sendRequest(string $url, array $data, string $method = 'POST')
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