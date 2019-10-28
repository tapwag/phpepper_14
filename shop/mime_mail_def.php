<?php
// Filename: mime_mail_def.php
//
// Modul: Klassendefinitionen (MIME, HTML_Email)
//
// Autoren: Sterling Hughes <sterling@php.net>
//          (Erweiterungen und starke Korrekturen:
//           Reto Glanzmann, Jose Fontanil <developers@phpeppershop.com>)
//
// Zweck: Definiert zwei Klassen: MIME, und HTML_Email
//        Beschreibungen und Anwendungen findet man direkt ueber der
//        jeweiligen Klassendefinition. (Siehe auch 'Wie wende ich die Klasse an')
//        Anmerkung: Codestyle ist in dieser Datei verschieden vom PhPepperShop-Stil.
//        Anmerkung2: Diese Datei hat noch Kompatibilitaetsprolbeme mit gewissen MTAs und
//                    Mailclients...
//
// Sicherheitsstatus:        *** USER ***
//
// Version: 1.4
//
// CVS-Version / Datum: $Id: mime_mail_def.php,v 1.3 2003/08/04 12:37:32 fontajos Exp $
//
// -----------------------------------------------------------------------
//
// Lizenz
// ======
// PhPepperShop Shopsystem
// Copyright (C) 2001 Sterling Hughes <sterling@php.net>,
// Extensions: (C) Reto Glanzmann, Jose Fontanil <developers@phpeppershop.com>
//
// -----------------------------------------------------------------------
// Damit jedes andere PhPepperShop Modul ueberpruefen kann ob dieses hier
// schon "included" ist wird folgende Vairable auf true gesetzt.
// (Name = Name des Moduls ohne .php)
$mime_mail_def = true;

// Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off
// funktioniert, werden die Request Arrays $HTTP_GET_VARS und dann $HTTP_POST_VARS
// in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
extract($_GET);
extract($_POST);

// Zufallszahl initialisieren
// Noetig bei PHP < 4.2.2
mt_srand((double)microtime()*1000000);

// -----------------------------------------------------------------------

// Wie wende ich die MIME Klasse an:
// Gegebene Daten: $to, $email, $subject, $bcc, $body
// $mm = new MIME($to, $email, $subject, $bcc, $body);
// $mm->attachment("picture.jpg", $contents, "image/jpeg", "base64", "us-ascii");
// $mm->send();
//
// Die Funktion attachment(...) verlangt nach fuenf Argumenten:
//   1.) Name des Attachments
//   2.) Inhalt des Attachments
//   3.) MIME-Type des Attachments (optional, default: application/octet-stream)
//   4.) Encoding der Daten        (optional, default: base64) -> siehe auch: http://www.freesoft.org/CIE/RFC/1521/5.htm
//   5.) Zeichensatz               (optional, default: iso-8859-1)
//   Man kann einem E-Mail mehrere Attachments hinzufuegen. (Mehrfachaufruf)
//
// $bcc beschreibt eine Kopienversendung an die angegebene E-Mail Adresse. (optional)

/**
 *  MIME class for building muiltipart MIME messages.
 *
 * @author Sterling Hughes <sterling@php.net> (kleine Erweiterungen Jose Fontanil)
 */
class MIME {
    // {{{ properties

    var $attachments = array();
    var $to = '';
    var $from = '';
    var $bcc = '';
    var $subject = '';
    var $body = '';
    var $alt_content_type = '';

    // }}}
    // {{{ MIME()

    /**
     * Constructor class that adds the values of the
     * required parts.
     *
     * @param $to string Who to send the message to.
     * @param $from string Who sent the message.
     * @param $subject string subject of the message
     * @param $bcc string Whom to send a blind carbon copy.
     * @param $body string the body of the message.
     * @param $alt_content_type string alternative content-type of mail
     *
     * @return object the new MIME object.
     */
    function MIME ($to, $from='', $subject='', $bcc='', $body='', $alt_content_type='') {
        $this->to = $to;
        $this->from = $from;
        $this->bcc = $bcc;         // Erweiterung von fjo
        $this->subject = $subject;
        $this->body = $body;
        $this->alt_content_type = $alt_content_type;  // Erweiterung von fjo
    }// End function MIME (constructor)

    // }}}
    // {{{ attachment()
    /**
     *  Add an attachment to the message
     *
     * @param $name string Name of the attachment.
     * @param $body string Body of the attachment.
     * @param $type string Content-type of the attachment. (default=octet-stream)
     * @param $encoding string encoding of the attachment. (default=base64)
     * @param $charset string used charset of the attachment. (default=iso-8859-1)
     *
     * @return null
     */
    function attachment ($name = "",
                         $contents = "",
                         $type = "application/octet-stream",
                         $encoding = "base64",
                         $charset = "iso-8859-1")   {
        $this->attachments[] =
                         array("filename" => $name,
                             "data"     => $contents,
                             "type"     => $type,
                             "encoding" => $encoding,
                             "charset"  => $charset);
    }// End function attachment

    // }}}
    // {{{ _build()

    function _build () {
        // mt_rand wurde mittels mt_srand(...) am Anfang dieser Datei initialisiert
        $boundary = '----=_NextPart_b'.(md5(uniqid(mt_rand())).getmypid());

        if ($this->from != "")
            $ret = "From: ".$this->from."\r\n";
        if ($this->bcc != "")
            $ret.= "Bcc: ".$this->bcc."\r\n";
        $ret.= "MIME-Version: 1.0\r\n";
        if ($this->alt_content_type != "") {
            $ret.= "Content-type: ".$this->alt_content_type."; ";
        }
        else {
            $ret.= 'Content-type: multipart/mixed; ';
        }
        $ret.= "boundary=\"$boundary\"\r\n";
        $ret.= "This is a multi-part message in MIME format.\r\n\r\n";
        $ret.= "--$boundary\r\n";

        $ret.= "Content-type: text/plain; charset=\"iso-8859-1\"\r\n";
        $ret.= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $ret.= $this->body."\r\n--$boundary";

        foreach ($this->attachments as $attachment) {
            // Wenn ein Attachment base64 encodiert werden soll, dies nun machen.
            if ($attachment['encoding'] == 'base64') {
                $attachment['data'] = base64_encode($attachment['data']);
                $attachment['data'] = chunk_split($attachment['data']);
            }
            $data = "\r\nContent-type: ".$attachment['type']."; charset=\"".$attachment['charset']."\"";
            if ($attachment['filename'] != "") {
                 $data.="; name=\"".$attachment['filename']."\"\r\n";
                 $data.="Content-Disposition: attachment; filename=\"".$attachment['filename']."\"";
            }
            $data.= "\r\n";
            $data.= "Content-Transfer-Encoding: ".$attachment['encoding'];
            $data.= "\r\n\r\n".$attachment['data']."\r\n";

            $ret.= $data;
            $ret.= "\r\n--$boundary";
        }// End foreach
        $ret.= "--\r\n";

        return($ret);
    }// End function _build

    // }}}
    // {{{  send()

    /**
     * Send the prebuilt message using the PHP-internal mail function.
     *
     * @return bool true on success, false on failure.
     */
    function send () {
        return mail($this->to,
                     $this->subject,
                     '',
                     $this->_build());
    }// End function send

}// End class MIME

// -----------------------------------------------------------------

// Wie wende ich die HTML_Email Klasse an:
// Info: Diese Klasse ist von der MIME-Klasse abgeleitet.
// Gegebene Daten: $to, $from, $subject, $bcc, $html_data
// $mm = new HTML_Email($to, $from, $subject, $bcc);
// $mm->html_data($html_data);
// $mm->plain_data(strip_tags($html_data));
// $mm->send();
// Man sollte HTML E-Mails IMMER mit einem Plaintext versehen.
// $bcc beschreibt eine Kopienversendung an die angegebene E-Mail Adresse.

/**
 *  A class for sending HTML e-mails
 *
 * @author Sterling Hughes <sterling@php.net> (Erweiterungen Jose Fontanil)
 */
class HTML_Email extends MIME {

    // {{{ HTML_Email()

    /**
     *  Constructor adds the To From and Subject
     *  fields of your e-mail
     *
     * @param $to string Who your sending the message to.
     * @param $from string Who the message is from.
     * @param $subject string The subject of the message
     * @param $bcc string Whom to send a blind carbon copy.
     * @param $alt_content_type string alternative content-type of mail
     *
     * @return object A new HTML_Email object.
     */
    function HTML_Email ($to, $from, $subject, $bcc) {
        $this->MIME($to, $from, $subject, $bcc, '', "multipart/alternative");
    }// End function HTML_Email

    // }}}
    // {{{ html_data()

    /**
     *  Add your HTML message to the e-mail
     *  by simply creating a new attachment with no filename
     *
     * @param $html string The HTML message.
     *
     * @return null
     */
    function html_data ($html) {
       /**
        * Parameterinfos attachment call
        * @param string name of the attachment
        * @param string content
        * @param string type
        * @param string encoding
        * @param string charset
        */
        $this->attachment("", $html, "text/html","8bit","iso-8859-1");
    }// End function html_data

    // }}}
    // {{{ plain_data()

   /**
     * Add your Plain text message to the e-mail
     *
     * @param $data string The Plain text message.
     *
     * @return null
     */
    function plain_data ($data) {
        $this->body = $data;
    }// End function plain_data

    // }}}

}// End class HTML_Email

// End of file -----------------------------------------------------------------
?>
