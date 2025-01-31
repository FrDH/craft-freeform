<?php

namespace Solspace\Freeform\Events\Submissions;

use Solspace\Freeform\Elements\Submission;
use Solspace\Freeform\Events\CancelableArrayableEvent;
use Solspace\Freeform\Form\Form;

class ProcessSubmissionEvent extends CancelableArrayableEvent
{
    private Form $form;

    private Submission $submission;

    public function __construct(Form $form, Submission $submission)
    {
        $this->form = $form;
        $this->submission = $submission;

        parent::__construct([]);
    }

    public function fields(): array
    {
        return ['form', 'submission'];
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getSubmission(): Submission
    {
        return $this->submission;
    }
}
