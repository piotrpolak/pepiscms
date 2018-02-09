<p>Item</p>
<a href="{module_name}/">Back</a>
<ul>
	<?php foreach( get_object_vars($item) as $key => $value ): ?>
	<li><?=$key?>: <?=$value?></li>
	<?php endforeach; ?>
</ul>
