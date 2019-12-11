<?php
$type = $_POST['type'];
$motherDom = $_POST['motherDom'];
$motherRec = $_POST['motherRec'];
$fatherDom = $_POST['fatherDom'];
$fatherRec = $_POST['fatherRec'];
$genes = [];
Global $json;
$json = json_decode(file_get_contents('data.json'));

switch($type)
{
	// standard
	case 'std':
	{
		$dominant = array($motherDom, $fatherDom);
		$recessive = array($motherRec, $fatherRec);

		foreach($dominant as $dom)
		{
			$domGene = getGene($dom);
			foreach($recessive as $rec)
			{
				$recGene = getGene($rec);
				$genes[] = $domGene->order < $recGene->order ? $dom.$rec : $rec.$dom;
			}
		}

		break;
	}

	// dominant to dominant
	case 'dtd':
	{
		$motherColours = getColours($motherDom, $fatherDom);
		$fatherColours = getColours($motherRec, $fatherRec);

		foreach($motherColours as $dom)
		{
			$domGene = getGene($dom);
			foreach($fatherColours as $rec)
			{
				$recGene = getGene($rec);
				$genes[] = $dom.$rec;
			}
		}

		break;
	}

	// dominant to recessive
	case 'dtr': 
	{
		$motherColours = getColours($motherDom, $motherRec);
		$fatherColours = getColours($fatherDom, $fatherRec);

		foreach($motherColours as $dom)
		{
			$domGene = getGene($dom);
			foreach($fatherColours as $rec)
			{
				$recGene = getGene($rec);
				$genes[] = $domGene->order < $recGene->order ? $dom.$rec : $rec.$dom;
			}
		}

		break;
	}

	// mix
	case 'mix':
	{
		$mother = getColours($motherDom, $fatherRec);
		$father = getColours($fatherDom, $motherRec);

		foreach($mother as $dom)
		{
			$domGene = getGene($dom);
			foreach($father as $rec)
			{
				$recGene = getGene($rec);
				$genes[] = $domGene->order < $recGene->order ? $dom.$rec : $rec.$dom;
			}
		}

		break;
	}

	// mix all
	case 'mia':
	{
		// mother
		$m_motherRec = getColours($motherDom, $motherRec);
		$m_fatherRec = getColours($motherDom, $fatherRec);
		$m = array_merge($m_motherRec, $m_fatherRec);

		// father
		$f_motherRec = getColours($fatherDom, $motherRec);
		$f_fatherRec = getColours($fatherDom, $fatherRec);
		$f = array_merge($f_motherRec, $f_fatherRec);

		foreach($m as $dom)
		{
			$domGene = getGene($dom);
			foreach($f as $rec)
			{
				$recGene = getGene($rec);
				$genes[] = $domGene->order < $recGene->order ? $dom.$rec : $rec.$dom;
			}
		}

		break;
	}
}

echo json_encode(array_count_values($genes));

function getColours($first, $second)
{
	global $json;

	$firstGene = getGene($first);
	$secondGene = getGene($second);
	
	$colours = [];
	foreach($json as $c)
	{
		$colours[] = $c->short;
	}

	$firstLocation = array_search($firstGene, $json);
	$secondLocation = array_search($secondGene, $json);

	return $firstGene->order < $secondGene->order ? array_slice($colours, $firstLocation, $secondLocation - $firstLocation + 1) : array_slice($colours, $secondLocation, $firstLocation - $secondLocation + 1);
}

function getGene($gene)
{
	global $json;
	foreach($json as $item)
	{
		if($item->short === $gene)
		{
			$obj = $item;
		}
	}
	return $obj;
}