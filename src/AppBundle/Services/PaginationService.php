<?php


namespace AppBundle\Services;


class PaginationService
{
    /**
     * @param $page
     * @param $numberOfPages
     * @return array
     */
    public function getHrefPosition($page, $numberOfPages)
    {
        if( $page == 1 ){
            $position = [0, 4];
        } else if( $page ==  $numberOfPages && $page - 4 >= 1 ){
            $position = [4, 0];
        } else if( $page ==  $numberOfPages && $page - 3 >= 1 ){
            $position = [3, 0];
        } else if( $page ==  $numberOfPages && $page - 2 >= 1 ){
            $position = [2, 0];
        } else if( $page ==  $numberOfPages && $page - 1 >= 1 ){
            $position = [1, 0];
        } else if( $page - 2 >= 1 && $page + 2 <= $numberOfPages){
            $position = [2, 2];
        } else if( $page - 3 >= 1 && $page + 1 <= $numberOfPages){
            $position = [3, 1];
        } else if( $page - 4 >= 1 && $page <= $numberOfPages){
            $position = [4, 0];
        } else if( $page - 1 == 1 ) {
            $position = [1, 3];
        }

        return $position;
    }
}