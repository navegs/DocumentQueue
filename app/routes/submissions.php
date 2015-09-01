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

$app->get('/submission/:id', $authenticated(), function ($subId) use ($app) {
    if (!isset($subId) || !is_numeric($subId)) {
            $app->flash('global', 'Submission id invalid or not provided!');

            $app->redirect($app->urlFor('home'));
    }

    $submission = Submission::find(intval($subId));

    if (!empty($submission)) {
        $submission->load('user', 'queue', 'queue.queueable', 'attachments', 'comments', 'comments.user');
        $submission->comments = $submission->comments->sortBy(function ($comment) {
            return $comment->created_at;
        })->reverse();

         // Loop through this user's queue collection and see if this user owns the queue
         // that this submission belongs to
        $queueOwnedByThisUser = false;
        $app->auth->queues->filter(function ($currentqueue) use ($submission, &$queueOwnedByThisUser) {
            if ($currentqueue->id_queue == $submission->queue->id_queue) {
                $queueOwnedByThisUser = true;
            }
        });

        $submissionOwnedByThisUser = ($submission->user->id_user == $app->auth->id_user) ? true : false;

        if ($app->auth->hasRole('ADMIN') || $submissionOwnedByThisUser || ($app->auth->hasRole(['ADVISOR', 'INSTRUCTOR']) && $queueOwnedByThisUser)) {
            // List of the current user's' queues
            $userQueues = $app->auth->queues->sortBy('name', $descending = true);
            // List of courses that the current users owns
            $mycourses = Course::where('id_coordinator', $app->auth->id_user)->with('queues')->get()->sortBy('name');

            $app->render('home.view.submission.html.twig', [
                'userqueues' => $userQueues,
                'mycourses' => $mycourses,
                'submission' => $submission,
                'queueOwnedByThisUser' => $queueOwnedByThisUser,
                'submissionOwnedByThisUser' => $submissionOwnedByThisUser
            ]);
        } else {
            // Redirect to Home if user has no permission to view this submission
            return $app->response->redirect($app->urlFor('home'));
        }
    } else {
        // Redirect to Home if no valid submission was found with this id
        
        $app->flash('global', 'Unknown Submission');
        
        return $app->response->redirect($app->urlFor('home'));
    }
})->name('home.view.submission');

$app->get('/attachment/:id', $authenticated(), function ($attachmentId) use ($app) {
    if (!isset($attachmentId) || !is_numeric($attachmentId)) {
            $app->flash('global', 'Attachment id invalid or not provided!');

            $app->redirect($app->urlFor('home'));
    }

    $attachment = Attachment::find(intval($attachmentId));

    if (!empty($attachment)) {
        $attachment->load('submission', 'submission.queue');

         // Loop through this user's queue collection and see if this user owns the queue
         // that this submission belongs to
        $queueOwnedByThisUser = false;
        $app->auth->queues->filter(function ($currentqueue) use ($attachment, &$queueOwnedByThisUser) {
            if ($currentqueue->id_queue == $attachment->submission->queue->id_queue) {
                $queueOwnedByThisUser = true;
            }
        });

        // Check for one of the following conditions before allowing the user to view the attachment
        // 1. User is an Administrator
        // 2. User viewing the attachment is the same user that submitted the attachment
        // 3. User has the ADVISOR or INSTRUCTOR roles and is the owner of the queue for this submission
        if ($app->auth->hasRole('ADMIN') || ($attachment->submission->id_user == $app->auth->id_user) || ($app->auth->hasRole(['ADVISOR', 'INSTRUCTOR']) && $queueOwnedByThisUser)) {
            $app->response->headers->set('Content-Description', 'File Transfer');
            $app->response->headers->set('Content-Type', $attachment->content_type);
            $app->response->headers->set('Content-Length', $attachment->size);
            $app->response->headers->set('Content-Disposition', 'attachment; filename='.$attachment->name);
            $app->response->headers->set('Content-Transfer-Encoding', 'binary');
            $app->response->setBody(base64_decode($attachment->content));
        } else {
            // Redirect to Home if user has no permission to view
            return $app->response->redirect($app->urlFor('home'));
        }
    } else {
        // Redirect to Home if no valid attachment was found with this id

        return $app->response->redirect($app->urlFor('home'));
    }
})->name('home.view.attachment');

$app->get('/submission/create/:id', $authenticated(), function ($queueId) use ($app) {
    if (!is_numeric($queueId)) {
        $app->flash('global', 'Invalid Queue Id');

        return $app->response->redirect($app->urlFor('home'));
    }

    $queue = Queue::with('elements', 'queueable')->find(intval($queueId));

    $app->render('home.view.createSubmission.html.twig', [
        'queue' => $queue
    ]);
})->name('home.view.submission.create');

$app->post('/submission/addcomment/:id', $authenticated(), function ($subId) use ($app) {
    if (!isset($subId) || !is_numeric($subId)) {
        $app->flash('global', 'Invalid Submission Id');

        return $app->response->redirect($app->urlFor('home'));
    }

    $request = $app->request;
    $comment = trim(strip_tags($request->post('comment')));

    if (!empty($comment) && (strlen($comment) <= 200)) {
        $submission = Submission::find(intval($subId));
        $submission->comments()->save(new Comment([
                                    'comment' => $comment,
                                    'id_user' => $app->auth->id_user,
                                    'created_at' => date('Y-m-d G:i:s')
        ]));
    }

    $app->response->redirect($app->urlFor('home.view.submission', array('id' => $subId)));
    
})->name('submission.addcomment');

$app->post('/submission/save/:id', $authenticated(), function ($queueId) use ($app) {
    if (!isset($queueId) || !is_numeric($queueId)) {
            $app->flash('global', 'Submission queue id invalid or not provided!');

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
    $filetotal = strip_tags($request->post('filetotal'));
    $comment = strip_tags($request->post('comment'));

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
                                'size' => $file['size'],
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

    // Add attachments for the new submission
    if (isset($attachments)) {
        $submission->attachments()->saveMany($attachments);
    }

    $app->flash('global', $queue->name.' Submitted');

    $app->redirect($app->urlFor('home.view.queue', array('id' => $queue->id_queue)));
})->name('home.view.submission.save');
