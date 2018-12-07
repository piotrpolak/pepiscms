<?php
/** @var Document $document */
/** @var MenuItem $item */

/** @var Menu $menu */
$menu = $document->getMenu();

?><!DOCTYPE html>
<html lang="<?= $document->getLanguageCode() ?>">
<head>
	<meta charset="utf-8">
	<title><?= $document->getTitle() ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta name="description" content="<?= $document->getDescription() ?>">
	<meta name="keywords" content="<?= $document->getKeywords() ?>">
	<base href="<?= base_url() ?>">
	<?php /*<link rel="icon" href="<?= site_theme_url() ?>img/favicon.ico" type="image/x-icon">*/ ?>

	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous">

	<?= $document->getPageStyles() ?>
	<?= $document->getPageJavaScript() ?>

    <meta property="og:url" content="<?= current_url() ?>"/>
    <meta property="og:type" content="article"/>
    <meta property="og:title" content="<?= $document->getTitle() ?>"/>
    <meta property="og:description" content="<?= $document->getDescription() ?>"/>

    <? // https://stackoverflow.com/a/18846258 ?>
    <?php if (!empty($document->getImageRelativePath())): ?>
    <meta property="og:image" content="<?= base_url() . $document->getImageRelativePath() ?>"/>
    <?php endif; ?>

	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>

<div class="container">

	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?=base_url()?>"><?=get_instance()->config->item('site_name')?></a>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<?php /* for infinite depth menu you should implement an recursive helper */ ?>
					<?php foreach ($menu->getChildren() as $item): ?>
						<li class="<?php if ($item->getId() == $document->getMenuItemId()): ?>active<?php endif; ?><?php if ($item->hasChildren()): ?> dropdown<?php endif; ?>">
							<a href="<?= $item->getRelativeUrl() ?>"<?php if ($item->hasChildren()): ?> class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"<?php endif; ?>><?= $item->getLabel() ?></a>
							<?php if ($item->hasChildren()): ?>
								<ul class="dropdown-menu">
									<?php foreach ($item->getChildren() as $subItem): ?>
										<li<?php if ($subItem->getId() == $document->getMenuItemId()): ?> class="active"<?php endif; ?>>
											<a href="<?= $subItem->getRelativeUrl() ?>"><?= $subItem->getLabel() ?></a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>

				</ul>
			</div>
		</div>
	</nav>

	<article>
		<?= $document->getContents() ?>
	</article>

	<footer>
		<div class="col-xs-6">
			<ul>
				<?php /* for infinite depth menu you should implement an recursive helper */ ?>
				<?php foreach ($menu->getChildren() as $item): ?>
					<li class="<?php if ($item->getId() == $document->getMenuItemId()): ?>active<?php endif; ?>">
						<a href="<?= $item->getRelativeUrl() ?>"><?= $item->getLabel() ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="col-xs-6 pull-right">
			<p>&copy; <?=date('Y')?> <?=get_instance()->config->item('site_name')?></p>
			<p>SEO text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec fringilla augue eget commodo porta. Ut hendrerit ex leo, quis dignissim sapien vulputate in. Pellentesque blandit at lectus non volutpat.</p>
		</div>
	</footer>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha256-KXn5puMvxCw+dAYznun+drMdG1IFl3agK0p/pqT9KAo= sha512-2e8qq0ETcfWRI4HJBzQiA3UoyFk6tbNyG+qSaIBZLyW9Xf3sWZHN/lxe9fTh1U45DpPf07yj94KsUHHWe4Yk1A==" crossorigin="anonymous"></script>

</body>
</html>