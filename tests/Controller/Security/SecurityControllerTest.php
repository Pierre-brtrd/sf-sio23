<?php

namespace App\Tests\Controller\Security;

use Exception;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class SecurityControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;

    protected ?AbstractDatabaseTool $databaseTool = null;

    protected ?UserRepository $userRepo = null;

    /**
     * Exécutée avant chaque test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();

        $this->userRepo = self::getContainer()->get(UserRepository::class);

        /* On injecte la class DbToolCollection dans la propriété pour l'utiliser dans les tests */
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadAliceFixture([
            \dirname(\dirname(__DIR__)) . '/Fixtures/UserFixtures.yaml',
            \dirname(\dirname(__DIR__)) . '/Fixtures/ArticleFixtures.yaml',
        ]);
    }

    public function testLoginPageResponse()
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function testLoginHeadingContent()
    {
        $this->client->request('GET', '/login');

        $this->assertSelectorTextContains('h1', 'Se connecter');
    }

    public function testAdminUserPageNotConnected()
    {
        $this->client->request('GET', '/admin/user');

        $this->assertResponseRedirects();
    }

    public function testAdminArticlePageNotConnected()
    {
        $this->client->request('GET', '/admin/article');

        $this->assertResponseRedirects();
    }

    public function testAdminTagPageNotConnected()
    {
        $this->client->request('GET', '/admin/categorie');

        $this->assertResponseRedirects();
    }

    public function testAdminCommentPageNotConnected()
    {
        $this->client->request('GET', '/admin/article/1/comments');

        $this->assertResponseRedirects();
    }

    public function testAdminUserPageBadLogin()
    {
        /* On récupère un utilisateur USER */
        $user = $this->userRepo->find(3);

        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/user');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminArticlePageBadLogin()
    {
        /* On récupère un utilisateur USER */
        $user = $this->userRepo->find(3);

        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/article');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminTagPageBadLogin()
    {
        /* On récupère un utilisateur USER */
        $user = $this->userRepo->find(3);

        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/categorie');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminCommentsPageBadLogin()
    {
        /* On récupère un utilisateur USER */
        $user = $this->userRepo->find(3);

        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/article/1/comments');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminUserPageGoodLogin()
    {
        $user = $this->userRepo->findOneBy(['email' => 'admin@test.com']);

        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/user');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminArticlePageGoodLogin()
    {
        $user = $this->userRepo->findOneBy(['email' => 'admin@test.com']);

        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/article');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminTagPageGoodLogin()
    {
        $user = $this->userRepo->findOneBy(['email' => 'admin@test.com']);

        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/categorie');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminCommentPageGoodLogin()
    {
        $user = $this->userRepo->findOneBy(['email' => 'admin@test.com']);

        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/article/1/comments');

        $this->assertResponseIsSuccessful();
    }

    public function testRegisterPageResponse()
    {
        $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    public function testRegisterHeadingPage()
    {
        $this->client->request('GET', '/register');

        $this->assertSelectorTextContains('h1', 'Création d\'un compte');
    }

    public function testRegisterNewValideUser()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form([
            'registration_user[firstName]' => 'John',
            'registration_user[lastName]' => 'Doe',
            'registration_user[email]' => 'john@example.com',
            'registration_user[password][first]' => 'Test1234',
            'registration_user[password][second]' => 'Test1234',
        ]);

        $this->client->submit($form);

        $newUser = $this->userRepo->findOneBy(['email' => 'john@example.com']);

        if (!$newUser) {
            throw new Exception('User not found');
        }

        $this->assertResponseRedirects();
    }

    public function testRegisterNewInvalideEmailUser()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form([
            'registration_user[firstName]' => 'John',
            'registration_user[lastName]' => 'Doe',
            'registration_user[email]' => 'john@com',
            'registration_user[password][first]' => 'Test1234',
            'registration_user[password][second]' => 'Test1234',
        ]);

        $this->client->submit($form);

        $this->assertSelectorTextContains('.invalid-feedback', 'Veuillez rentrer un email valide');
    }

    public function testRegisterNewNotBlankPaswordUser()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form([
            'registration_user[firstName]' => 'John',
            'registration_user[lastName]' => 'Doe',
            'registration_user[email]' => 'john@example.com',
            'registration_user[password][first]' => '',
            'registration_user[password][second]' => '',
        ]);

        $this->client->submit($form);

        $this->assertSelectorTextContains('.invalid-feedback', 'Veuillez renseigner un mot de passe');
    }

    public function testRegisterNewMinLengthPaswordUser()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('S\'inscrire')->form([
            'registration_user[firstName]' => 'John',
            'registration_user[lastName]' => 'Doe',
            'registration_user[email]' => 'john@example.com',
            'registration_user[password][first]' => 'a',
            'registration_user[password][second]' => 'a',
        ]);

        $this->client->submit($form);

        $this->assertSelectorTextContains('.invalid-feedback', 'Votre mot de passe doit contenir au moins 6 caractères');
    }
}
