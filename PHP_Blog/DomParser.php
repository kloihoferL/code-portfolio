<?php

class DomParser
{
    private string $file;
    private DOMDocument $document;

    public function __construct(string $file){
        $this->file = $file;
        $this->document = new DOMDocument();
    }

    public function load():bool{
        if($this->document->load($this->file))
            return true;
        return false;
    }

    public function importXML() {
        if (!$this->load()) {
            echo "Document could not be loaded!";
            return;
        }

        $entries = $this->document->getElementsByTagName("entry");
        foreach ($entries as $entry) {
            $title = $entry->getElementsByTagName("title")[0]->nodeValue;
            $userid = $entry->getElementsByTagName("userid")[0]->nodeValue;
            $noteText = $entry->getElementsByTagName("noteText")[0]->nodeValue;
            $state = $entry->getElementsByTagName("state")[0]->nodeValue;
            $createdOn = $entry->getElementsByTagName("createdOn")[0]->nodeValue;
            $lastEdit = $entry->getElementsByTagName("lastEdit")[0]->nodeValue;
            $lastEditUser = $entry->getElementsByTagName("lastEditUser")[0]->nodeValue;

            $insertQuery = "INSERT INTO blogEntry (title, userid, noteText, state, createdOn, lastEdit, lastEditUser) VALUES ('$title', '$userid', '$noteText', '$state', '$createdOn', '$lastEdit', '$lastEditUser')";
            Database::insertQuery($insertQuery);
        }
    }
}