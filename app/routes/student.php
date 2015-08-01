<?PHP

$app->get('/student', function () use ($app) {
	$app->render("student.html.twig");
})->name('student');