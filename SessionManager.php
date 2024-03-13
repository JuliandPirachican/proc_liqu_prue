<?php
class SessionManager {
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["codi_usua"] = isset($_SESSION['codi_usua']) ? $_SESSION['codi_usua']:'sistemas';
    }

    public function get_user(){
        return $_SESSION['codi_usua'];
    }
}
?>