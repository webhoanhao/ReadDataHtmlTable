<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace WebHoanHao\ReadDataHtmlTable;

/**
 * Description of ReturnTableHtml
 *
 * @author BaoBui
 */
class ReturnTableHtml {
    private $url;
    private $tableID;
    private $tableHtml;
    function __construct() {

    }
    public static function withUrl($htmlUrl, $tableId) {
        if (NULL != $htmlUrl) {
            // Get html using curl
            $c = curl_init($htmlUrl);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $html = curl_exec($c);
            if (curl_error($c)) {
                curl_close($c);
                return;
            }
            // Check return status
            $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
            if (200 <= $status && 300 > $status) {
                $kq = TRUE;
            } else {
                $kq = FALSE;
            }
            curl_close($c);
            if ($kq) {
                $tableHtml = getTable($html,$tableId);
                return $tableHtml;
            } else {
                return;
            }
        } else {
            return;
        }
    }
    public static function withFilename($htmlFilename, $tableId) {
        
        return $htmlFilename;
    }
    private static function getTable($htmlContent, $tableId) {
        if (NULL != $htmlContent) {
            // Remove newlines, returns, and breaks
            $htmlContent = str_replace(array("\n", "\r", "\t"), '', $htmlContent);
            $htmlContent = str_ireplace("\0D", '', $htmlContent);
            $htmlContent = str_ireplace("  ", ' ', $htmlContent);
            // Pull table out of HTML
            if (strcmp('', $tableId)) {
                $table_str = '<table';
            } else {
                $table_str = '<table id="' . $tableId;
            }
            $start_pos = stripos($htmlContent, $table_str);
            $end_pos = stripos($htmlContent, '</table>', $start_pos) + strlen('</table>');
            $length = $end_pos - $start_pos;
            $table = substr($htmlContent, $start_pos, $length);
            return $table;
        } else {
            return;
        }
    }
    function getUrl() {
        return $this->url;
    }

    function getTableHtml() {
        return $this->tableHtml;
    }

    function setUrl($url) {
        $this->url = $url;
    }

    function setTableHtml($tableHtml) {
        $this->tableHtml = $tableHtml;
    }


}
