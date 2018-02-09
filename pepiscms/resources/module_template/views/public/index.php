<p>Items</p>
<ul>
	<?php foreach( $items as $item ): ?>
	<li><a href="{module_name}/item/id-<?=$item->id?>"><?=$item->id?></a></li>
	<?php endforeach; ?>
</ul>
