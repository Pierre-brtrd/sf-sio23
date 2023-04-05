<?php

namespace App\Tests\Panther;

use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\Panther\PantherTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class ArticlePantherTest extends PantherTestCase
{
    protected $client;

    protected ?AbstractDatabaseTool $databaseTool = null;

    /**
     * Exécutée avant chaque test.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->client = self::createPantherClient();

        /* On injecte la class DbToolCollection dans la propriété pour l'utiliser dans les tests */
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadAliceFixture([
            \dirname(__DIR__)  . '/Fixtures/UserFixtures.yaml',
            \dirname(__DIR__)  . '/Fixtures/ArticleFixtures.yaml',
            \dirname(__DIR__)  . '/Fixtures/TagFixtures.yaml',
        ]);
    }

    public function testArticlePageShowMore()
    {
        $crawler = $this->client->request('GET', '/articles');

        $this->client->waitFor('.btn-show-more', 2);

        $this->client->executeScript("document.querySelector('.btn-show-more').click()");

        $this->client->waitForEnabled('.btn-show-more', 2);

        $crawler = $this->client->refreshCrawler();

        $this->assertCount(12, $crawler->filter('.blog-list .blog-card'));
    }

    public function testArticleLatestPageShowMore()
    {
        $this->client->request('GET', '/articles');

        $this->client->waitFor('.btn-show-more', 2);

        foreach (range(1, 3) as $i) {
            $this->client->executeScript("document.querySelector('.btn-show-more').click()");

            $this->client->waitForEnabled('.btn-show-more', 2);
        }

        $this->assertSelectorIsNotVisible('.btn-show-more');
    }

    public function testArticleSearchAjax()
    {
        $crawler = $this->client->request('GET', '/articles');

        $this->client->waitFor('.form-filter', 2);

        $search = $this->client->findElement(WebDriverBy::cssSelector('.form-filter input[type="text"]'));

        $search->sendKeys('Article de test');

        $this->client->waitFor('.content-response', 3);

        /* On attend 0,5 seconde pour la fin de l'animation flipper */
        sleep(1);

        $crawler = $this->client->refreshCrawler();

        $this->assertCount(1, $crawler->filter('.blog-list .blog-card'));
    }
}
