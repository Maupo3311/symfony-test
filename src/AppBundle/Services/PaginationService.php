<?php

namespace AppBundle\Services;

/**
 * Class PaginationService
 * @package AppBundle\Services
 */
class PaginationService
{
    /**
     * @param $page
     * @param $numberOfPages
     * @return array
     */
    public function getHrefPosition($page, $numberOfPages)
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
        }

        return $position;
    }
}