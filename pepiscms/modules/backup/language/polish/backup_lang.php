<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Automatically generated language file

 * @date 2015-04-11
 * @file backup_lang.php
 */
$lang['backup_database_settings_not_found']          = 'Ustawienia połączenia z bazą danych nie zostały znalezione. Upewnij się, że plik ustawień jest poprawny. Czasami ustawienia bazy danych sypią się jeśli robisz  include w plikach konfiguracyjnych.';
$lang['backup_dump_disabled_on_windows']             = 'Zrzut bazy danych jest wyłączony na platformie Windows';
$lang['backup_dump_unable_to_make']                  = 'Nie udało się wykonać zrzut bazy danych. Sprawdź logi systemowe.';
$lang['backup_dump_works_only_with_mysql']           = 'Zrzuty bazy danych działają tylko z silnikiem MySQL';
$lang['backup_index_tip']                            = 'Tworzenie i przywracanie kopii bezpieczeństwa zostanie odnotowane w logach systemowych';
$lang['backup_module_description']                   = 'Umożliwia wykonanie zrzutu SQL całej bazy, utworzenie i przywracanie treści stron i struktury menu z pliku XML';
$lang['backup_module_name']                          = 'Kopia bezpieczeństwa';
$lang['backup_sql_backup']                           = 'Kopia bazy danych SQL';
$lang['backup_sql_do']                               = 'Wykonaj pełną kopię bazy danych';
$lang['backup_sql_do_description']                   = 'Zrzuca strukturę i treść całej bazy danych do pliku SQL';
$lang['backup_sql_do_groups_and_rights']             = 'Wykonaj kopię grup i praw dostępu';
$lang['backup_sql_do_groups_and_rights_description'] = 'Zrzuca strukturę i treść bazy danych zawierającą uprawnienia użytkownika do pliku SQL';
$lang['backup_xml_backup']                           = 'Kopia XML - strony i menu';
$lang['backup_xml_do']                               = 'Wykonaj backup stron';
$lang['backup_xml_do_description']                   = 'Tworzy plik kopii zapasowej treści witryny oraz struktury menu';
$lang['backup_xml_restore']                          = 'Przywróć treść stron z backupu';
$lang['backup_xml_restore_description']              = 'Przywraca menu i treść strony z pliku XML kopii bezpieczeństwa';
$lang['backup_xml_restore_not_a_valid_backup_file']  = 'Przesłany plik nie jest poprawnym plikiem kopii bezpieczeństwa. Przywracanie przerwane.';
$lang['backup_xml_restore_not_a_valid_xml_document'] = 'Przesłany plik nie jest poprawnym dokumentem XML lub nie wgrano żadnego pliku. Przywracanie przerwane.';
$lang['backup_xml_restore_tip']                      = 'Wgraj plik XML zawierający kopię bezpieczeństwa strony. Miej na uwadze, że cała struktura strony zostanie nadpisana.';
$lang['backup_xml_restore_unable_to_restore']        = 'Nie udało się przywrócić treści stron z backupu. Transakcja bazy danych nie powiodła się.';