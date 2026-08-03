<?php

    class Blog {

        public static function Categories() {
            global $db;
            $query_categories = $db->from('categories')
                                    ->select('categories.*, COUNT(post_id) as category_total')
                                    ->join('posts','FIND_IN_SET(category_id, post_categories)')
                                    ->orderby('category_order', 'ASC')
                                    ->groupby('category_id')
                                    ->all();

            return $query_categories;
        }


        public static function findPost($post_url) {
            global $db;

            return $db->from('posts')
                      ->select('posts.*, GROUP_CONCAT(category_name SEPARATOR ", ") as category_name, GROUP_CONCAT(category_url SEPARATOR ", ") as category_url')
                      ->join('categories', 'find_in_set(category_id, post_categories)')
                      ->where('post_url', $post_url)
                      ->where('post_status', 1)
                      ->first();

        }
    }
