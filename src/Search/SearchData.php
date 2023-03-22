<?php

namespace App\Search;

/**
 * Search Data class for article object
 */
class SearchData
{
    /**
     * Filter for the number of page results
     *
     * @var integer|null
     */
    private ?int $page = 1;

    /**
     * Filter for text query on title article
     *
     * @var string|null
     */
    private ?string $query = '';

    /**
     * Filter for tags on article
     *
     * @var array|null
     */
    private ?array $tags = [];

    /**
     * Filter for author on article
     *
     * @var array|null
     */
    private ?array $authors = [];

    /**
     * Get the value of author
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Set the value of author
     */
    public function setAuthors($author): self
    {
        $this->authors = $author;

        return $this;
    }

    /**
     * Get the value of tags
     *
     * @return ?array
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * Set the value of tags
     *
     * @param ?array $tags
     *
     * @return self
     */
    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get the value of query
     *
     * @return ?string
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * Set the value of query
     *
     * @param ?string $query
     *
     * @return self
     */
    public function setQuery(?string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get the value of page
     *
     * @return ?int
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * Set the value of page
     *
     * @param ?int $query
     *
     * @return self
     */
    public function setPage(?int $page): self
    {
        $this->page = $page;

        return $this;
    }
}
