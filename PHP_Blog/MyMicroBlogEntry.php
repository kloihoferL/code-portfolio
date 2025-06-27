<?php

class MyMicroBlogEntry
{

    public function __construct(private int $ID, private string $title, private int $userID, private string $noteText,private string $state,
                                private DateTime $creationDate, private DateTime $lastEditTime, private int $lastEditUserID, ){

    }

    public function __toString() {
        $currentUserID = $_SESSION['user']['userid'] ?? null;

        // Nur anzeigen, wenn der aktuelle Benutzer mit dem Benutzer des Blog-Eintrags übereinstimmt
        $buttons = '';
        if ($currentUserID == $this->userID) {
            $buttons = "<div class='blog-entry-buttons'>".
                "<button>Edit</button>".
                "<button>Delete</button>".
                "<button>Highlight</button>".
                "</div>";
        }

        return "<div class='blog-entry'>" .
            "<h2>{$this->title}</h2>" .
            /*"<p>UserID: {$this->userID}</p>".*/
            "<p>Note: {$this->noteText}</p>" .
            "<p>Status: {$this->state}</p>" .
            "<p>Created on: {$this->creationDate->format('Y-m-d H:i:s')}</p>" .
            "<p>Last Edited on: {$this->lastEditTime->format('Y-m-d H:i:s')}</p>" .
            "<p>by User: {$this->lastEditUserID}</p>" .
            $buttons .
            "</div>";
    }


}