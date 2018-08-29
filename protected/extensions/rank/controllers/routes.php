<?php
$app->post('/website-ranking', function ($request, $response, $args) {
    $params = $request->getParams();
    $rank_tool = new \Extensions\Components\RankTool();
    if (strpos($params['Ranking']['website'], "www.") === false) {
        if (strpos($params['Ranking']['website'], "http") === false) {
            $params['Ranking']['website'] = 'http://www.'.$params['Ranking']['website'];
        }
    }
    $website = parse_url(strtolower($params['Ranking']['website']));
    if (isset($params['Ranking']) && !empty($params['Ranking']['q'])) {
        $base_src = 'https://www.google.co.id/search';
        $q = strtolower($params['Ranking']['q']);
        $qparams = ['q' => $q, 'oq' => $q];

        $items = []; $positions = [];
        for ($i = 0; $i<=7; $i++) {
            $start = 10 * $i;
            $qparams['start'] = $start;
            $src = $base_src."?".http_build_query($qparams);
            $html = file_get_html($src);
            $sub_items = []; $no = 0;
            foreach($html->find('h3 a') as $element) {
                $href = parse_url($element->href, PHP_URL_QUERY);
                if (!empty($href)) {
                    $explode1 = explode("&", $href);
                    foreach ($explode1 as $exp1) {
                        $x = explode("=", $exp1);
                        if (is_array($x) && $x[0] == 'q'
                            && strpos($x[1], "youtube.com") == false
                            && $rank_tool->is_valid_domain_name($x[1])) {
                            array_push($sub_items, $x[1]);
                            $no = $no + 1;
                            if (strpos(strtolower($x[1]), $website['host'])) {
                                $found = [ 'page' => $i+1, 'rank' => $no];
                                array_push($positions, $found);
                            }
                        } else {
                            if (strpos($x, "http") != false
                                && $rank_tool->is_valid_domain_name($x)) {
                                $no = $no + 1;
                                array_push($sub_items, $x);
                                if (strpos(strtolower($x[1]), $website['host'])) {
                                    $found = [ 'page' => $i+1, 'rank' => $no];
                                    array_push($positions, $found);
                                }
                            }
                        }
                    }
                }
            }
            $items[$i+1] = $sub_items;
        }

        return $this->view->render($response, 'website-ranking.phtml', [
            'params' => $params['Ranking'],
            'results' => $items,
            'positions' => $positions
        ]);
    }
});

foreach (glob(__DIR__.'/*_controller.php') as $controller) {
	$cname = basename($controller, '.php');
	if (!empty($cname)) {
		require_once $controller;
	}
}

foreach (glob(__DIR__.'/../components/*.php') as $component) {
    $cname = basename($component, '.php');
    if (!empty($cname)) {
        require_once $component;
    }
}

?>
