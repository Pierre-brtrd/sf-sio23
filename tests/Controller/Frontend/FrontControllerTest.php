<?php

namespace App\Tests\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class FrontControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;

    protected ?AbstractDatabaseTool $databaseTool = null;

    /**
     * Exécutée avant chaque test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();

        /* On injecte la class DbToolCollection dans la propriété pour l'utiliser dans les tests */
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadAliceFixture([
            \dirname(\dirname(__DIR__)) . '/Fixtures/UserFixtures.yaml',
            \dirname(\dirname(__DIR__)) . '/Fixtures/ArticleFixtures.yaml',
            \dirname(\dirname(__DIR__)) . '/Fixtures/TagFixtures.yaml',
        ]);
    }

    public function testHomePageResponse()
    {
        /* On demande au client d'aller sur l'url de la homepage en method GET */
        $this->client->request('GET', '/');

        /* On s'attend à avoir un code de réponse 200 */
        $this->assertResponseIsSuccessful();
    }

    public function testArticleHomePage()
    {
        $crawler = $this->client->request('GET', '/');

        /* On s'attend à avoir 3 articles donc 3 éléments HTML avec la classes .blog-card */
        $this->assertCount(3, $crawler->filter('.blog-card'));
    }
}
