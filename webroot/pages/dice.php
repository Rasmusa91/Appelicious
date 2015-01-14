<?php
	$dice = new CDice(6);
	$rolls = (isset($_GET["rolls"]) ? $_GET["rolls"] : null)
?>

<h2>Kasta tärning</h2>

<p>
	Hur många kast vill du göra <a href = "?p=dice&amp;rolls=1">1 kast</a>, <a href = "?p=dice&amp;rolls=3">3 kast</a> eller <a href = "?p=dice&amp;rolls=6">6 kast</a>?
</p>

<?php if (isset($rolls)): ?>

	<p>
		Du gjorde <?= $rolls ?> kast och fick följande resultat.
	<p>
	
	<ul>
		<?php for($i = 0; $i < $rolls && $i < 100; $i++): ?>
			<li><?= $dice->roll(); ?></li>
		<?php endfor; ?>
	</ul>
<?php endif; ?>