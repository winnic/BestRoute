<?php
// Name: Minimum Spanning Tree Function Using Prim Algorithm (BUG FIXED)
// Description:The function would give the minimum spanning tree using prim algorithm (i guess ...) in two dimensional array and the total weight of the edges.
// By: As'ad Djamalilleil
//
// Inputs:The inputs are :
//1. Two dimensional array that converted from a graph.
//2. The starting node of the graph.
//3. A variable the will reference the result in two dimensional array.
//4. A variable that will reference the total weight of the edges in minimum spanning tree.
//
// Returns:The minimum spanning tree in two dimensional array and the total weight of the edges.
//
//This code is copyrighted and has// limited warranties.Please see http://www.Planet-Source-Code.com/vb/scripts/ShowCode.asp?txtCodeId=2625&lngWId=8//for details.//**************************************


	function mst($matrix, $startAtNode, &$result, &$totalWeight){
		$initMatrix = $matrix;
$numberOfNode = count($matrix); $idx = 0;
		$result = array_fill(0,$numberOfNode,array_fill(0,$numberOfNode,0));
		$visitedNode[0] = $startAtNode;
		for($i = 0; $i < $numberOfNode - 1; $i++){
			$shortestEdge = max($initMatrix);
			for($col = 0; $col <= $idx; $col++){
				$startAtNode = $visitedNode[$col];
				for($row = 0; $row < $numberOfNode; $row++)
					if(($matrix[$row][$startAtNode] < $shortestEdge) && ($matrix[$row][$startAtNode] > 0)){
						$shortestEdge = $matrix[$row][$startAtNode];
						$prevNode = $startAtNode;
						$nextNode = $row;
					}
			}
			$idx++;
			$visitedNode[$idx] = $startAtNode = $nextNode;
			$result[$prevNode][$nextNode] = $result[$nextNode][$prevNode] = $initMatrix[$prevNode][$nextNode];
			$totalWeight += $matrix[$prevNode][$nextNode];
			for($a = 0; $a <= $idx; $a++)
				for($b = 0; $b <= $idx; $b++)
					$matrix[$visitedNode[$a]][$visitedNode[$b]] = $matrix[$visitedNode[$b]][$visitedNode[$a]] = 0;
		}
	}
///////////////////////////////////////////////////////////////////////////////
	$matrix = array(array(0,7,0,5,0,0,0),array(7,0,8,9,7,0,0),array(0,8,0,0,5,0,0),
					array(5,9,0,0,15,6,0),array(0,7,5,15,0,8,9),array(0,0,0,6,8,0,11),
					array(0,0,0,0,9,11,0));
	mst($matrix, 1, $result, $totalWeight);
	echo "<pre>";
	for($i = 0; $i < 7; $i++){
		for($j = 0; $j < 7; $j++){
			echo $result[$i][$j],"\t";
$total += $result[$i][$j];
		}
		echo "\n<br>";
	}
	echo "</pre>\n\n<br>";
	echo "Total weight = $totalWeight <br>";
	echo "Total weight = " . $total/2;
?>