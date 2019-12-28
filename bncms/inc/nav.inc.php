<?php

/** @license bncms
 *
 * Copyright (c) Damian Hunziker and other bncms contributors
 * https://github.com/damianhunziker/bncms
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

echo "<table cellspacing='5' cellpadding='0'>
<tr>
<td>
";
$query="SELECT * FROM site WHERE name != ''";
$aSites=dbQuery($query);
echo "<a href='index.php' class='siteNav'>Zum Shop</a>";
foreach ($aSites as $key => $value) {
	echo "<a href='$value[url]' class='siteNav'>$value[name]</a>";
}
echo "<br>";
echo "<img src='img/trans.gif' width='100' height='1'>
</td>
</tr>
</table>";
?>
