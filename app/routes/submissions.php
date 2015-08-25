<?php

use DocManager\Course\Course;
use DocManager\Queue\Queue;
use DocManager\Submission\Submission;
use DocManager\Submission\Attachment;
use DocManager\Comment\Comment;

$app->get('/submissions', $authenticated(), function () use ($app) {
    $userQueues = $app->auth->queues->sortBy('name');
    $mycourses = Course::where('id_coordinator', $app->auth->id_user)->with('queues')->get()->sortBy('name');
    $submissions = Submission::where('id_user', $app->auth->id_user)->with('user', 'queue', 'queue.queueable')->get();

    $app->render('home.view.submissions.html.twig', [
        'userqueues' => $userQueues,
        'mycourses' => $mycourses,
        'submissions' => $submissions
    ]);
})->name('home.view.submissions');

$app->get('/submission/create/:id', $authenticated(), function ($queueId) use ($app) {
    $queue = Queue::with('elements', 'queueable')->find(intval($queueId));

    $app->render('home.view.createSubmission.html.twig', [
        'queue' => $queue
    ]);
})->name('home.view.submission.create');

$app->post('/submission/save/:id', $authenticated(), function ($queueId) use ($app) {
    if (!isset($queueId)) {
            $app->flash('global', 'Submission queue id not provided!');

            $app->redirect($app->urlFor('home'));
    }

    // Get the Queue that were creating a submission for
    $queue = Queue::with('elements')->find(intval($queueId));

    // If no queue with provided id is found, redirect user to home with error
    if (empty($queue)) {
        $app->flash('global', 'Could not find queue with id ('.$queueId.')!');

        $app->redirect($app->urlFor('home'));
    }

    // Make sure that the queues required number of attachments matches the number submitted
    // If not, redirect with error message
    if (count($_FILES) != count($queue->elements)) {
            $app->flash('global', 'Incorrect number of files attached!');

            $app->redirect($app->urlFor('home.view.submission.create', array('id' => $queue->id_queue)));
    }

    $request = $app->request;
    $filetotal = $request->post('filetotal');
    $comment = $request->post('comment');

    /*
        Used to check for errors and provide useful descriptions in case a file upload fails.
        This should be placed in a helper class at some point, but just putting it here for now.
     */
    $phpFileUploadErrors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    );

    /*
        Create an array of attachments from uploaded files to add to this submission
     */
    if (isset($filetotal) && ($filetotal > 0)) {
        $filetotal = intval($filetotal);

        for ($i=0; $i<$filetotal; $i++) {
            $file = $_FILES["file$i"];

            // Check for file upload error and redirect with error message
            if ($file['error'] != 0) {
                $app->flash('global', $phpFileUploadErrors[$file['error']]);

                $app->redirect($app->urlFor('home.view.submission.create', array('id' => $queue->id_queue)));
            }

            /*
                Get the files Mime type
                Preference is to check the files mime type on the server side using the php_fileinfo module
                and not assume that the browser passed the correct type. 
                The PHP fileinfo module must be availabe for this.
                Check to see if the fileinfo module is loaded. If so, use that, otherwise use what the client
                provided for file type
             */
            if (extension_loaded('fileinfo')) {
                $contenttype = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['tmp_name']);
            } else {
                $contenttype = $file['type'];
            }

            $attachments[$i] = new Attachment([
                                'name' => $file['name'],
                                'content_type' => $contenttype,
                                'content' => base64_encode(file_get_contents($file['tmp_name']))
            ]);
        }
    }

    // Update or create the submission and get reference
    $submission = Submission::create([
        'id_user' => $app->auth->id_user,
        'id_queue' => $queue->id_queue,
        'status' => Submission::STATUS_AWAITING_REVIEW
    ]);

    // Add comment if provided
    if (isset($comment) and !empty(trim($comment))) {
        $submission->comments()->save(new Comment([
                                        'comment' => $comment,
                                        'id_user' => $app->auth->id_user,
                                        'created_at' => $submission->created_at
        ]));
    }
//var_dump($submission->freshTimestamp());
    // Add attachments for the new submission
    if (isset($attachments)) {
        $submission->attachments()->saveMany($attachments);
    }

    $app->flash('global', 'Submission Submitted');

    $app->redirect($app->urlFor('home.view.queue', array('id' => $queue->id_queue)));
})->name('home.view.submission.save');
