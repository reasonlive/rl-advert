<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

function universal_link_bar($count, $page, $URL, $perpage, $show_link, $page_text='&rp=', $before_page='') {

	$bar_content = '';
	$style = 'class="selpage"';
	$now_page_style = 'class="selpage-act"';
	$pages_count = ceil($count / $perpage);
	//if ($pages_count == 1) return false;
	$sperator = '';
	$y = $show_link;

	$begin = $page - intval($show_link / 2);
	unset($show_dots);

	if ($page != 1 && $pages_count>=1) {

		if($page==2) {
			$bar_content .= '<td class="orient"><a href="'.$URL.$before_page.'" style="border: none;"><span class="text14">&larr;</span>&nbsp;Предыдущая</span></a></td><td class="orient" width="90%">';
		}else{
			$bar_content .= '<td class="orient"><a href="'.$URL.$before_page.$page_text.($page - 1).'" style="border: none;"><span class="text14">&larr;</span>&nbsp;Предыдущая</span></a></td><td class="orient" width="90%">';
		}

	}elseif($pages_count>=0) {

		$bar_content .= '<td class="orient"><span class="navygray"><span class="text14">&larr;</span>&nbsp;Предыдущая</span></td><td class="orient" width="90%">';

		if ($pages_count <= $show_link + 1) $show_dots = 'no';

		if (($begin > 2) && !isset($show_dots) && ($pages_count - $show_link > 2)) {
			$bar_content .= '<a '.$style.' href="'.$URL.$before_page.'">1</a>';
		}
	}


	for ($j = 1; $j < $page; $j++) {

		if ((($begin + 1 + $show_link - $j) > $pages_count+1) && ($pages_count-$show_link + $j > 0)) {

			$page_link = $pages_count - $show_link + $j;

			if (!isset($show_dots) && ($pages_count-$show_link) >= 1) {
				$bar_content .= '<a '.$style.' href="'.$URL.$before_page.'">1</a>';
				if(($pages_count-$show_link) > 1) $bar_content .= '&hellip;';
				$show_dots = "no";
			}else{
			}

			$bar_content .= '<a '.$style.' href="'.$URL.$before_page.$page_text.$page_link.'">'.$page_link.'</a>'.$sperator;
		}else{
			continue;
		}
	}

	for ($j = 1; $j <= $show_link; $j++) {
		$i = $begin + $j-1; // Номер ссылки

		if ($i < 1) {
			$show_link++;
			continue;
		}


		if (!isset($show_dots) && $begin > 1 ) {
			if ($begin + $show_link - 1 <= $pages_count) {
				$bar_content .= '<a '.$style.' href="'.$URL.$before_page.'">1</a>';
				if(($pages_count-$show_link) > 1) $bar_content .= '&hellip;';
				$show_dots = "no";
			}
		}

		if ($i > $pages_count) break;


		if ($pages_count>1)  {
			if ($i == $page && $pages_count>1) {
				$bar_content .= '<span '.$now_page_style.'>'.$i.'</span>';
			}elseif($i==1) {
				$bar_content .= '<a '.$style.' href="'.$URL.$before_page.'">'.$i.'</a>';
			}else{
				$bar_content .= '<a '.$style.' href="'.$URL.$before_page.$page_text.$i.'">'.$i.'</a>';
			}
		}


		if (($i != $pages_count) && ($j != $show_link)) $bar_content .= "$sperator";


		if (($j == $show_link) && ($i+1) <= $pages_count && ($pages_count-$y) > 1 ) {
			$bar_content .= '&hellip;';
		}
	}

	if ($begin + $show_link +0 <= $pages_count) {
		$bar_content .= '<a '.$style.' href="'.$URL.$before_page.$page_text.$pages_count.'">'.$pages_count.'</a>';
	}

	if ($page != $pages_count && $pages_count>=0) {
		$bar_content .= '</td><td class="orient"><a href="'.$URL.$before_page.$page_text.($page + 1).'" style="border: none;">Следующая&nbsp;<span class="text14">&rarr;</span></a></td>';
	}elseif($pages_count>=0) {
		$bar_content .= '</td><td class="orient"><span class="navygray">Следующая&nbsp;<span class="text14">&rarr;</span></span></td>';
	}

	echo '<table class="navigation"><tr>'.$bar_content.'</tr></table>';
}

?>