<?php
require('fpdf.php');

class PDF_Draw extends FPDF {
	//PDFDraw
		// Sets line style
		// Parameters:
		// - style: Line style. Array with keys among the following:
		//   . width: Width of the line in user units
		//   . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
		//   . join: miter, round or bevel
		//   . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
		//           For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
		//                        (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
		//   . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
		//   . color: Draw color. Array with components (red, green, blue)
		function SetLineStyle($style) {
			extract($style);
			if (isset($width)) {
				$width_prev = $this->LineWidth;
				$this->SetLineWidth($width);
				$this->LineWidth = $width_prev;
			}
			if (isset($cap)) {
				$ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
				if (isset($ca[$cap]))
					$this->_out($ca[$cap] . ' J');
			}
			if (isset($join)) {
				$ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
				if (isset($ja[$join]))
					$this->_out($ja[$join] . ' j');
			}
			if (isset($dash)) {
				$dash_string = '';
				if ($dash) {
					$tab = explode(',', $dash);
					$dash_string = '';
					foreach ($tab as $i => $v) {
						if ($i > 0)
							$dash_string .= ' ';
						$dash_string .= sprintf('%.2F', $v);
					}
				}
				if (!isset($phase) || !$dash)
					$phase = 0;
				$this->_out(sprintf('[%s] %.2F d', $dash_string, $phase));
			}
			if (isset($color)) {
				list($r, $g, $b) = $color;
				$this->SetDrawColor($r, $g, $b);
			}
		}

		// Draws a line
		// Parameters:
		// - x1, y1: Start point
		// - x2, y2: End point
		// - style: Line style. Array like for SetLineStyle
		function Line($x1, $y1, $x2, $y2, $style = null) {
			if ($style)
				$this->SetLineStyle($style);
			parent::Line($x1, $y1, $x2, $y2);
		}

		// Draws a rectangle
		// Parameters:
		// - x, y: Top left corner
		// - w, h: Width and height
		// - style: Style of rectangle (draw and/or fill: D, F, DF, FD)
		// - border_style: Border style of rectangle. Array with some of this index
		//   . all: Line style of all borders. Array like for SetLineStyle
		//   . L: Line style of left border. null (no border) or array like for SetLineStyle
		//   . T: Line style of top border. null (no border) or array like for SetLineStyle
		//   . R: Line style of right border. null (no border) or array like for SetLineStyle
		//   . B: Line style of bottom border. null (no border) or array like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
			if (!(false === strpos($style, 'F')) && $fill_color) {
				list($r, $g, $b) = $fill_color;
				$this->SetFillColor($r, $g, $b);
			}
			switch ($style) {
				case 'F':
					$border_style = null;
					parent::Rect($x, $y, $w, $h, $style);
					break;
				case 'DF': case 'FD':
					if (!$border_style || isset($border_style['all'])) {
						if (isset($border_style['all'])) {
							$this->SetLineStyle($border_style['all']);
							$border_style = null;
						}
					} else
						$style = 'F';
					parent::Rect($x, $y, $w, $h, $style);
					break;
				default:
					if (!$border_style || isset($border_style['all'])) {
						if (isset($border_style['all']) && $border_style['all']) {
							$this->SetLineStyle($border_style['all']);
							$border_style = null;
						}
						parent::Rect($x, $y, $w, $h, $style);
					}
					break;
			}
			if ($border_style) {
				if (isset($border_style['L']) && $border_style['L'])
					$this->Line($x, $y, $x, $y + $h, $border_style['L']);
				if (isset($border_style['T']) && $border_style['T'])
					$this->Line($x, $y, $x + $w, $y, $border_style['T']);
				if (isset($border_style['R']) && $border_style['R'])
					$this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
				if (isset($border_style['B']) && $border_style['B'])
					$this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
			}
		}

		// Draws a B�zier curve (the B�zier curve is tangent to the line between the control points at either end of the curve)
		// Parameters:
		// - x0, y0: Start point
		// - x1, y1: Control point 1
		// - x2, y2: Control point 2
		// - x3, y3: End point
		// - style: Style of rectangule (draw and/or fill: D, F, DF, FD)
		// - line_style: Line style for curve. Array like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
			if (!(false === strpos($style, 'F')) && $fill_color) {
				list($r, $g, $b) = $fill_color;
				$this->SetFillColor($r, $g, $b);
			}
			switch ($style) {
				case 'F':
					$op = 'f';
					$line_style = null;
					break;
				case 'FD': case 'DF':
					$op = 'B';
					break;
				default:
					$op = 'S';
					break;
			}
			if ($line_style)
				$this->SetLineStyle($line_style);

			$this->_Point($x0, $y0);
			$this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
			$this->_out($op);
		}

		// Draws an ellipse
		// Parameters:
		// - x0, y0: Center point
		// - rx, ry: Horizontal and vertical radius (if ry = 0, draws a circle)
		// - angle: Orientation angle (anti-clockwise)
		// - astart: Start angle
		// - afinish: Finish angle
		// - style: Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
		// - line_style: Line style for ellipse. Array like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		// - nSeg: Ellipse is made up of nSeg B�zier curves
		function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
			if ($rx) {
				if (!(false === strpos($style, 'F')) && $fill_color) {
					list($r, $g, $b) = $fill_color;
					$this->SetFillColor($r, $g, $b);
				}
				switch ($style) {
					case 'F':
						$op = 'f';
						$line_style = null;
						break;
					case 'FD': case 'DF':
						$op = 'B';
						break;
					case 'C':
						$op = 's'; // small 's' means closing the path as well
						break;
					default:
						$op = 'S';
						break;
				}
				if ($line_style)
					$this->SetLineStyle($line_style);
				if (!$ry)
					$ry = $rx;
				$rx *= $this->k;
				$ry *= $this->k;
				if ($nSeg < 2)
					$nSeg = 2;

				$astart = deg2rad((float) $astart);
				$afinish = deg2rad((float) $afinish);
				$totalAngle = $afinish - $astart;

				$dt = $totalAngle/$nSeg;
				$dtm = $dt/3;

				$x0 *= $this->k;
				$y0 = ($this->h - $y0) * $this->k;
				if ($angle != 0) {
					$a = -deg2rad((float) $angle);
					$this->_out(sprintf('q %.2F %.2F %.2F %.2F %.2F %.2F cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
					$x0 = 0;
					$y0 = 0;
				}

				$t1 = $astart;
				$a0 = $x0 + ($rx * cos($t1));
				$b0 = $y0 + ($ry * sin($t1));
				$c0 = -$rx * sin($t1);
				$d0 = $ry * cos($t1);
				$this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
				for ($i = 1; $i <= $nSeg; $i++) {
					// Draw this bit of the total curve
					$t1 = ($i * $dt) + $astart;
					$a1 = $x0 + ($rx * cos($t1));
					$b1 = $y0 + ($ry * sin($t1));
					$c1 = -$rx * sin($t1);
					$d1 = $ry * cos($t1);
					$this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
								$this->h - (($b0 + ($d0 * $dtm)) / $this->k),
								($a1 - ($c1 * $dtm)) / $this->k,
								$this->h - (($b1 - ($d1 * $dtm)) / $this->k),
								$a1 / $this->k,
								$this->h - ($b1 / $this->k));
					$a0 = $a1;
					$b0 = $b1;
					$c0 = $c1;
					$d0 = $d1;
				}
				$this->_out($op);
				if ($angle !=0)
					$this->_out('Q');
			}
		}

		// Draws a circle
		// Parameters:
		// - x0, y0: Center point
		// - r: Radius
		// - astart: Start angle
		// - afinish: Finish angle
		// - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
		// - line_style: Line style for circle. Array like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		// - nSeg: Ellipse is made up of nSeg B�zier curves
		function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
			$this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
		}

		// Draws a polygon
		// Parameters:
		// - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
		// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
		// - line_style: Line style. Array with one of this index
		//   . all: Line style of all lines. Array like for SetLineStyle
		//   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
			$np = count((array)$p) / 2;
			if (!(false === strpos($style, 'F')) && $fill_color) {
				list($r, $g, $b) = $fill_color;
				$this->SetFillColor($r, $g, $b);
			}
			switch ($style) {
				case 'F':
					$line_style = null;
					$op = 'f';
					break;
				case 'FD': case 'DF':
					$op = 'B';
					break;
				default:
					$op = 'S';
					break;
			}
			$draw = true;
			if ($line_style)
				if (isset($line_style['all']))
					$this->SetLineStyle($line_style['all']);
				else { // 0 .. (np - 1), op = {B, S}
					$draw = false;
					if ('B' == $op) {
						$op = 'f';
						$this->_Point($p[0], $p[1]);
						for ($i = 2; $i < ($np * 2); $i = $i + 2)
							$this->_Line($p[$i], $p[$i + 1]);
						$this->_Line($p[0], $p[1]);
						$this->_out($op);
					}
					$p[$np * 2] = $p[0];
					$p[($np * 2) + 1] = $p[1];
					for ($i = 0; $i < $np; $i++)
						if (!empty($line_style[$i]))
							$this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
				}

			if ($draw) 
			{
				$this->_Point($p[0], $p[1]);
				for ($i = 2; $i < ($np * 2); $i = $i + 2)
					$this->_Line($p[$i], $p[$i + 1]);
				$this->_Line($p[0], $p[1]);
				$this->_out($op);
			}
		}

		// Draws a regular polygon
		// Parameters:
		// - x0, y0: Center point
		// - r: Radius of circumscribed circle
		// - ns: Number of sides
		// - angle: Orientation angle (anti-clockwise)
		// - circle: Draw circumscribed circle or not
		// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
		// - line_style: Line style. Array with one of this index
		//   . all: Line style of all lines. Array like for SetLineStyle
		//   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		// - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
		// - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
		// - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
		function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
			if ($ns < 3)
				$ns = 3;
			if ($circle)
				$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
			$p = null;
			for ($i = 0; $i < $ns; $i++) {
				$a = $angle + ($i * 360 / $ns);
				$a_rad = deg2rad((float) $a);
				$p[] = $x0 + ($r * sin($a_rad));
				$p[] = $y0 + ($r * cos($a_rad));
			}
			$this->Polygon($p, $style, $line_style, $fill_color);
		}

		// Draws a star polygon
		// Parameters:
		// - x0, y0: Center point
		// - r: Radius of circumscribed circle
		// - nv: Number of vertices
		// - ng: Number of gaps (ng % nv = 1 => regular polygon)
		// - angle: Orientation angle (anti-clockwise)
		// - circle: Draw circumscribed circle or not
		// - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
		// - line_style: Line style. Array with one of this index
		//   . all: Line style of all lines. Array like for SetLineStyle
		//   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		// - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
		// - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
		// - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
		function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
			if ($nv < 2)
				$nv = 2;
			if ($circle)
				$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
			$p2 = null;
			$visited = null;
			for ($i = 0; $i < $nv; $i++) {
				$a = $angle + ($i * 360 / $nv);
				$a_rad = deg2rad((float) $a);
				$p2[] = $x0 + ($r * sin($a_rad));
				$p2[] = $y0 + ($r * cos($a_rad));
				$visited[] = false;
			}
			$p = null;
			$i = 0;
			do {
				$p[] = $p2[$i * 2];
				$p[] = $p2[($i * 2) + 1];
				$visited[$i] = true;
				$i += $ng;
				$i %= $nv;
			} while (!$visited[$i]);
			$this->Polygon($p, $style, $line_style, $fill_color);
		}

		// Draws a rounded rectangle
		// Parameters:
		// - x, y: Top left corner
		// - w, h: Width and height
		// - r: Radius of the rounded corners
		// - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
		// - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
		// - border_style: Border style of rectangle. Array like for SetLineStyle
		// - fill_color: Fill color. Array with components (red, green, blue)
		function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
			if ('0000' == $round_corner) // Not rounded
				$this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
			else { // Rounded
				if (!(false === strpos($style, 'F')) && $fill_color) {
					list($red, $g, $b) = $fill_color;
					$this->SetFillColor($red, $g, $b);
				}
				switch ($style) {
					case 'F':
						$border_style = null;
						$op = 'f';
						break;
					case 'FD': case 'DF':
						$op = 'B';
						break;
					default:
						$op = 'S';
						break;
				}
				if ($border_style)
					$this->SetLineStyle($border_style);

				$MyArc = 4 / 3 * (sqrt(2) - 1);

				$this->_Point($x + $r, $y);
				$xc = $x + $w - $r;
				$yc = $y + $r;
				$this->_Line($xc, $y);
				if ($round_corner[0])
					$this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
				else
					$this->_Line($x + $w, $y);

				$xc = $x + $w - $r ;
				$yc = $y + $h - $r;
				$this->_Line($x + $w, $yc);

				if ($round_corner[1])
					$this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
				else
					$this->_Line($x + $w, $y + $h);

				$xc = $x + $r;
				$yc = $y + $h - $r;
				$this->_Line($xc, $y + $h);
				if ($round_corner[2])
					$this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
				else
					$this->_Line($x, $y + $h);

				$xc = $x + $r;
				$yc = $y + $r;
				$this->_Line($x, $yc);
				if ($round_corner[3])
					$this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
				else {
					$this->_Line($x, $y);
					$this->_Line($x + $r, $y);
				}
				$this->_out($op);
			}
		}

		/* PRIVATE METHODS */

		// Sets a draw point
		// Parameters:
		// - x, y: Point
		function _Point($x, $y) {
			$this->_out(sprintf('%.2F %.2F m', $x * $this->k, ($this->h - $y) * $this->k));
		}

		// Draws a line from last draw point
		// Parameters:
		// - x, y: End point
		function _Line($x, $y) {
			$this->_out(sprintf('%.2F %.2F l', $x * $this->k, ($this->h - $y) * $this->k));
		}

		// Draws a B�zier curve from last draw point
		// Parameters:
		// - x1, y1: Control point 1
		// - x2, y2: Control point 2
		// - x3, y3: End point
		function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
			$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
		}


	//PDF_Gradients
		protected $gradients = array();

		function LinearGradient($x, $y, $w, $h, $col1=array(), $col2=array(), $coords=array(0,0,1,0))
		{
			$this->Clip($x,$y,$w,$h);
			$this->Gradient(2,$col1,$col2,$coords);
		}

		function MultiLinearGradient($x, $y, $w, $h, $col=array(),$coords=array(0,0,1,0))
		{
			$this->Clip($x,$y,$w,$h);
			$this->MultiGradient($col,$coords);
		}

		function RadialGradient($x, $y, $w, $h, $col1=array(), $col2=array(), $coords=array(0.5,0.5,0.5,0.5,1)){
			$this->Clip($x,$y,$w,$h);
			$this->Gradient(3,$col1,$col2,$coords);
		}

		function CoonsPatchMesh($x, $y, $w, $h, $col1=array(), $col2=array(), $col3=array(), $col4=array(), $coords=array(0.00,0.0,0.33,0.00,0.67,0.00,1.00,0.00,1.00,0.33,1.00,0.67,1.00,1.00,0.67,1.00,0.33,1.00,0.00,1.00,0.00,0.67,0.00,0.33), $coords_min=0, $coords_max=1){
			$this->Clip($x,$y,$w,$h);		
			$n = count($this->gradients)+1;
			$this->gradients[$n]['type']=6; //coons patch mesh
			//check the coords array if it is the simple array or the multi patch array
			if(!isset($coords[0]['f'])){
				//simple array -> convert to multi patch array
				if(!isset($col1[1]))
					$col1[1]=$col1[2]=$col1[0];
				if(!isset($col2[1]))
					$col2[1]=$col2[2]=$col2[0];
				if(!isset($col3[1]))
					$col3[1]=$col3[2]=$col3[0];
				if(!isset($col4[1]))
					$col4[1]=$col4[2]=$col4[0];
				$patch_array[0]['f']=0;
				$patch_array[0]['points']=$coords;
				$patch_array[0]['colors'][0]['r']=$col1[0];
				$patch_array[0]['colors'][0]['g']=$col1[1];
				$patch_array[0]['colors'][0]['b']=$col1[2];
				$patch_array[0]['colors'][1]['r']=$col2[0];
				$patch_array[0]['colors'][1]['g']=$col2[1];
				$patch_array[0]['colors'][1]['b']=$col2[2];
				$patch_array[0]['colors'][2]['r']=$col3[0];
				$patch_array[0]['colors'][2]['g']=$col3[1];
				$patch_array[0]['colors'][2]['b']=$col3[2];
				$patch_array[0]['colors'][3]['r']=$col4[0];
				$patch_array[0]['colors'][3]['g']=$col4[1];
				$patch_array[0]['colors'][3]['b']=$col4[2];
			}
			else{
				//multi patch array
				$patch_array=$coords;
			}
			$bpcd=65535; //16 BitsPerCoordinate
			//build the data stream
			$this->gradients[$n]['stream']='';
			for($i=0;$i<count($patch_array);$i++){
				$this->gradients[$n]['stream'].=chr($patch_array[$i]['f']); //start with the edge flag as 8 bit
				for($j=0;$j<count($patch_array[$i]['points']);$j++){
					//each point as 16 bit
					$patch_array[$i]['points'][$j]=(($patch_array[$i]['points'][$j]-$coords_min)/($coords_max-$coords_min))*$bpcd;
					if($patch_array[$i]['points'][$j]<0) $patch_array[$i]['points'][$j]=0;
					if($patch_array[$i]['points'][$j]>$bpcd) $patch_array[$i]['points'][$j]=$bpcd;
					$this->gradients[$n]['stream'].=chr(floor($patch_array[$i]['points'][$j]/256));
					$this->gradients[$n]['stream'].=chr(floor($patch_array[$i]['points'][$j]%256));
				}
				for($j=0;$j<count($patch_array[$i]['colors']);$j++){
					//each color component as 8 bit
					$this->gradients[$n]['stream'].=chr($patch_array[$i]['colors'][$j]['r']);
					$this->gradients[$n]['stream'].=chr($patch_array[$i]['colors'][$j]['g']);
					$this->gradients[$n]['stream'].=chr($patch_array[$i]['colors'][$j]['b']);
				}
			}
			//paint the gradient
			$this->_out('/Sh'.$n.' sh');
			//restore previous Graphic State
			$this->_out('Q');
		}

		function Clip($x,$y,$w,$h){
			//save current Graphic State
			$s='q';
			//set clipping area
			$s.=sprintf(' %.2F %.2F %.2F %.2F re W n', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k);
			//set up transformation matrix for gradient
			$s.=sprintf(' %.3F 0 0 %.3F %.3F %.3F cm', $w*$this->k, $h*$this->k, $x*$this->k, ($this->h-($y+$h))*$this->k);
			$this->_out($s);
		}

		function MultiGradient($col, $coords)
		{
			$type=count($col);
			foreach($col as $num => $value)
			{
				if(!isset($col{$num+1}[0]) or !isset($col{$num+1}[1]) or !isset($col{$num+1}[2]))
				{
					foreach($col{$num} as $i => $val)
					{
						switch($i)
						{
							case 'r':
								$col{$num+1}[0] = $val;
							break;
							case 'g':
								$col{$num+1}[1] = $val;
							break;
							case 'b':
								$col{$num+1}[2] = $val;
							break;
							default:
								$col{$num+1}[0] = 0;
							break;
						}
					}
				}
				$n = count($this->gradients)+1;
				$this->gradients[$n]['type']=$type;
				if(!isset($col{$num+1}[1]))
					$col{$num+1}[1]=$col{$num+1}[2]=$col{$num+1}[0];
				$this->gradients[$n]['col'.strval($num+1)]=sprintf('%.3F %.3F %.3F',($col{$num+1}[0]/255),($col{$num+1}[1]/255),($col{$num+1}[2]/255));
			}
			$this->gradients[$n]['coords']=$coords;
			//paint the gradient
			$this->_out('/Sh'.$n.' sh');
			//restore previous Graphic State
			$this->_out('Q');
		}

		function Gradient($type, $col1, $col2, $coords)
		{
			if(!isset($col1[0]) or !isset($col1[1]) or !isset($col1[2]))
			{
				foreach($col1 as $i => $val)
				{
					switch($i)
					{
						case 'r':
							$col1[0] = $val;
						break;
						case 'g':
							$col1[1] = $val;
						break;
						case 'b':
							$col1[2] = $val;
						break;
						default:
						break;
					}
				}
			}
			if(!isset($col2[0]) or !isset($col2[1]) or !isset($col2[2]))
			{
				foreach($col2 as $i => $val)
				{
					switch($i)
					{
						case 'r':
							$col2[0] = $val;
						break;
						case 'g':
							$col2[1] = $val;
						break;
						case 'b':
							$col2[2] = $val;
						break;
						default:
						break;
					}
				}
			}
			$n = count($this->gradients)+1;
			$this->gradients[$n]['type']=$type;
			if(!isset($col1[1]))
				$col1[1]=$col1[2]=$col1[0];
			$this->gradients[$n]['col1']=sprintf('%.3F %.3F %.3F',($col1[0]/255),($col1[1]/255),($col1[2]/255));
			if(!isset($col2[1]))
				$col2[1]=$col2[2]=$col2[0];
			$this->gradients[$n]['col2']=sprintf('%.3F %.3F %.3F',($col2[0]/255),($col2[1]/255),($col2[2]/255));
			$this->gradients[$n]['coords']=$coords;
			//paint the gradient
			$this->_out('/Sh'.$n.' sh');
			//restore previous Graphic State
			$this->_out('Q');
		}

		function _putshaders(){
			foreach($this->gradients as $id=>$grad){  
				if($grad['type']==2 || $grad['type']==3){
					$this->_newobj();
					$this->_put('<<');
					$this->_put('/FunctionType 2');
					$this->_put('/Domain [0.0 1.0]');
					$this->_put('/C0 ['.$grad['col1'].']');
					$this->_put('/C1 ['.$grad['col2'].']');
					$this->_put('/N 1');
					$this->_put('>>');
					$this->_put('endobj');
					$f1=$this->n;
				}
				
				$this->_newobj();
				$this->_put('<<');
				$this->_put('/ShadingType '.$grad['type']);
				$this->_put('/ColorSpace /DeviceRGB');
				if($grad['type']=='2'){
					$this->_put(sprintf('/Coords [%.3F %.3F %.3F %.3F]',$grad['coords'][0],$grad['coords'][1],$grad['coords'][2],$grad['coords'][3]));
					$this->_put('/Function '.$f1.' 0 R');
					$this->_put('/Extend [true true] ');
					$this->_put('>>');
				}
				elseif($grad['type']==3){
					//x0, y0, r0, x1, y1, r1
					//at this time radius of inner circle is 0
					$this->_put(sprintf('/Coords [%.3F %.3F 0 %.3F %.3F %.3F]',$grad['coords'][0],$grad['coords'][1],$grad['coords'][2],$grad['coords'][3],$grad['coords'][4]));
					$this->_put('/Function '.$f1.' 0 R');
					$this->_put('/Extend [true true] ');
					$this->_put('>>');
				}
				elseif($grad['type']==6){
					$this->_put('/BitsPerCoordinate 16');
					$this->_put('/BitsPerComponent 8');
					$this->_put('/Decode[0 1 0 1 0 1 0 1 0 1]');
					$this->_put('/BitsPerFlag 8');
					$this->_put('/Length '.strlen($grad['stream']));
					$this->_put('>>');
					$this->_putstream($grad['stream']);
				}
				$this->_put('endobj');
				$this->gradients[$id]['id']=$this->n;
			}
		}

		function _putresourcedict(){
			parent::_putresourcedict();
			$this->_put('/Shading <<');
			foreach($this->gradients as $id=>$grad)
				$this->_put('/Sh'.$id.' '.$grad['id'].' 0 R');
			$this->_put('>>');
		}

		function _putresources(){
			$this->_putshaders();
			parent::_putresources();
		}

	//PDF_Rotate
		var $angle=0;
		function Rotate($angle,$x=-1,$y=-1)
		{
			if($x==-1)
				$x=$this->x;
			if($y==-1)
				$y=$this->y;
			if($this->angle!=0)
				$this->_out('Q');
			$this->angle=$angle;
			if($angle!=0)
			{
				$angle*=M_PI/180;
				$c=cos($angle);
				$s=sin($angle);
				$cx=$x*$this->k;
				$cy=($this->h-$y)*$this->k;
				$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
			}
		}
		function _endpage()
		{
			if($this->angle!=0)
			{
				$this->angle=0;
				$this->_out('Q');
			}
			parent::_endpage();
		}
}

?>
