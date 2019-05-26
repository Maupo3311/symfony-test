<?php

namespace AppBundle\Services;

/**
 * Class PaginationService
 * @package AppBundle\Services
 */
class PaginationService
{
    /**
     * @var integer
     */
    protected $page;

    /**
     * @var integer
     */
    protected $numberOfPages;

    /**
     * @var boolean
     */
    protected $sort;

    /**
     * @var string
     */
    protected $currentField;

    /**
     * @var string
     */
    protected $currentOrder;

    /**
     * @var array
     */
    protected $position;

    /**
     * @var integer
     */
    protected $theNumberOnThePage;

    /**
     * PaginationService constructor.
     * @param int $page
     * @param int $quantity
     * @param int $theNumberOnThePage
     */
    public function __construct(int $page, int $quantity, int $theNumberOnThePage = 10)
    {
        $this->page               = $page;
        $this->numberOfPages      = ceil($quantity / $theNumberOnThePage);
        $this->theNumberOnThePage = $theNumberOnThePage;

        $this->setHrefPosition($this->page, $this->numberOfPages);
    }

    /**
     * @return array
     */
    public function getSort()
    {
        return [$this->currentField => $this->currentOrder];
    }

    /**
     * @param string $currentOrder
     * @param        $currentField
     * @return $this
     */
    public function setSort(string $currentOrder, $currentField)
    {
        $this->currentField = $currentField;
        $this->currentOrder = $currentOrder;
        $this->sort         = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getNextOrder()
    {
        if ($this->currentOrder === 'ASC') {
            return 'DESC';
        } else {
            return 'ASC';
        }
    }

    /**
     * Get pagination position
     *
     * @param $page
     * @param $numberOfPages
     * @return array
     */
    protected function setHrefPosition($page, $numberOfPages)
    {
        if ($page == 1) {
            $position = [0, 4];
        } elseif ($page == $numberOfPages && $page - 4 >= 1) {
            $position = [4, 0];
        } elseif ($page == $numberOfPages && $page - 3 >= 1) {
            $position = [3, 0];
        } elseif ($page == $numberOfPages && $page - 2 >= 1) {
            $position = [2, 0];
        } elseif ($page == $numberOfPages && $page - 1 >= 1) {
            $position = [1, 0];
        } elseif ($page - 2 >= 1 && $page + 2 <= $numberOfPages) {
            $position = [2, 2];
        } elseif ($page - 3 >= 1 && $page + 1 <= $numberOfPages) {
            $position = [3, 1];
        } elseif ($page - 4 >= 1 && $page <= $numberOfPages) {
            $position = [4, 0];
        } elseif ($page - 1 == 1) {
            $position = [1, 3];
        } else {
            $position = [2, 2];
        }

        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }

    /**
     * @param int $numberOfPages
     */
    public function setNumberOfPages(int $numberOfPages)
    {
        $this->numberOfPages = $numberOfPages;
    }

    /**
     * @return string
     */
    public function getCurrentField()
    {
        return $this->currentField;
    }

    /**
     * @param string $currentField
     */
    public function setCurrentField(string $currentField)
    {
        $this->currentField = $currentField;
    }

    /**
     * @return string
     */
    public function getCurrentOrder()
    {
        return $this->currentOrder;
    }

    /**
     * @param string $currentOrder
     */
    public function setCurrentOrder(string $currentOrder)
    {
        $this->currentOrder = $currentOrder;
    }

    /**
     * @return array
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param array $position
     */
    public function setPosition(array $position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getTheNumberOnThePage()
    {
        return $this->theNumberOnThePage;
    }

    /**
     * @param int $theNumberOnThePage
     */
    public function setTheNumberOnThePage(int $theNumberOnThePage)
    {
        $this->theNumberOnThePage = $theNumberOnThePage;
    }

}