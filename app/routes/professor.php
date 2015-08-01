<?PHP

$app->get('/professor', function () use ($app) {
	$app->render("professor.html.twig");
})->name('professor');