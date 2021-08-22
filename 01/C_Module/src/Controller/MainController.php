<?php

namespace Kty\Controller;

use Kty\App\DB;

class MainController extends MasterController
{
    public function index()
    {
        $this->render('main');
    }

    public function daejeonBakery()
    {
        $storeSql =
            "SELECT 
            s.id, s.name, s.connect, 
            (select avg(g.score) from grades g where s.id = g.store_id) gpa,
            (select count(*) from reviews where s.id = reviews.store_id) review, s.image, concat(l.borough, ' ', l.name) `location`,
            SUM(if(d.bread_id = b.id, d.cnt, 0)) sell, rank() OVER(ORDER BY sell desc) 'ranking'
            FROM delivery_items d, breads b
            JOIN stores s
            ON b.store_id = s.id
            JOIN users u
            ON u.id = s.user_id
            JOIN locations l
            ON s.user_id = u.id and u.location_id = l.id
            GROUP BY b.store_id
            ORDER BY sell DESC;";

        $storeList = DB::fetchAll($storeSql);
        $breadList = DB::fetchAll("SELECT * FROM breads");

        $this->render('daejeon_bakery', ['list' => $storeList, 'breadList' => $breadList, 'sql' => $storeSql]);
    }
}
