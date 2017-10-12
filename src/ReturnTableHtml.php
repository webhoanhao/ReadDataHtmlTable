<?php
namespace WebHoanHao\ReadDataHtmlTable;
const STR_FOR_COLSPAN = '98765432987654329876543298765432';
const STR_FOR_ROWSPAN = '98765432987654329876543299999999';
use HTMLPurifier;
use HTMLPurifier_Config;
/**
 * Description of ReturnTableHtml
 *
 * @author BaoBui
 */
class ReturnTableHtml {
    public function withUrl($htmlUrl, $tableId) {
        if (NULL != $htmlUrl && '' != $htmlUrl) {
            // Get html using curl
            $c = curl_init($htmlUrl);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $html = curl_exec($c);
            if (curl_error($c)) {
                curl_close($c);
                return null;
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
                $tableHtml = $this->getTable($html,$tableId);
                return $tableHtml;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    public function withFilename($htmlFilename, $tableId) {
        if (null != $htmlFilename && '' != $htmlFilename) {
            $handle = fopen($htmlFilename, "r");
            $contents = fread($handle, filesize($htmlFilename));
            fclose($handle);
            if (null != $contents && '' != $contents) {
                $tableHtml = $this->getTable($contents,$tableId);
                return $tableHtml;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    private function getTable($htmlContent, $tableId) {
        if (NULL != $htmlContent) {
            $htmlContent = $this->replaceSpanZero($htmlContent);
            //echo $htmlContent;
            $config = HTMLPurifier_Config::createDefault();
            $config->set('AutoFormat.RemoveEmpty', false); // remove empty tag pairs
            $purifier = new HTMLPurifier($config);
            $clean_html = $purifier->purify($htmlContent);
            $clean_html = $this->returnSpanZero($clean_html);
            // Remove newlines, returns, and breaks
            $htmlContent1 = str_replace(array("\n", "\r", "\t"), '', $clean_html);
            $htmlContent2 = str_ireplace("\0D", '', $htmlContent1);
            $htmlContent3= preg_replace('!\s+!', ' ', $htmlContent2);
            
            // Pull table out of HTML
            if (NULL != $tableId && '' != $tableId) {
                $table_str = '<table id="' . $tableId;
            } else {
                $table_str = '<table';
            }
            $start_pos = stripos($htmlContent3, $table_str);
            $end_pos = stripos($htmlContent3, '</table>', $start_pos) + strlen('</table>');
            $length = $end_pos - $start_pos;
            $table = substr($htmlContent3, $start_pos, $length);
            return $table;
        } else {
            return null;
        }
    }
    private function replaceSpanZero($inputHtml) {
        $inputHtml = str_ireplace('colspan="0"','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan= "0"','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan=\'0\'','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan= \'0\'','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan=0','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan = 0','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan ="0"','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan = "0"','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan =\'0\'','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan = \'0\'','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan =0','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('colspan = 0','colspan="'.STR_FOR_COLSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan="0"','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan= "0"','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan=\'0\'','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan= \'0\'','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan=0','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan= 0','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan ="0"','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan = "0"','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan =\'0\'','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan = \'0\'','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan =0','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        $inputHtml = str_ireplace('rowspan = 0','colspan="'.STR_FOR_ROWSPAN.'"',$inputHtml);
        return $inputHtml;
    }
    private function returnSpanZero($inputHtml) {
        $inputHtml = str_ireplace('colspan="'.STR_FOR_COLSPAN.'"','colspan="0"',$inputHtml);
        $inputHtml = str_ireplace('rowspan ="'.STR_FOR_ROWSPAN.'"','colspan="0"',$inputHtml);
        return $inputHtml;
    }
}
