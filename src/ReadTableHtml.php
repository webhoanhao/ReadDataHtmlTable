<?php
namespace WebHoanHao\ReadDataHtmlTable;
use DOMDocument;
/**
 * Description of ReadTableHtml
 *
 * @author BaoBui
 */
class ReadTableHtml {
    private $plainTextCols = array();
    private $ignoreCols = array();
    private $ignoreRows = array();
    private $headerRowIsData = false;
    private $tableType = 0;


    /**
     * @return mixed
     */
    public function getTableType() {
        return $this->tableType;
    }

    public function withUrl ($urlHttp, $tableID = null) {
        $htmlCodeObj = new ReturnTableHtml();
        $tableHtml = $htmlCodeObj->withUrl($urlHttp, $tableID);
        $tableArrayTR = $this->Table2ArrayTR($tableHtml);
        $arrayTableObj = $this->ArrayTR2ObjectArray($tableArrayTR);
        $resultIgnoredTableArray = $this->IgnoreRowsArrayObject($arrayTableObj,$this->ignoreRows);
        $objTableArray = $this->IgnoreColsArrayObject($resultIgnoredTableArray,$this->ignoreCols);
        return $objTableArray;
    }
    public function withFilename ($fileNameWithPath, $tableID = null) {
        $htmlCodeObj = new ReturnTableHtml();
        $tableHtml = $htmlCodeObj->withFilename($fileNameWithPath,$tableID);
        $tableArrayTR = $this->Table2ArrayTR($tableHtml);
        $arrayTableObj = $this->ArrayTR2ObjectArray($tableArrayTR);
        $resultIgnoredTableArray = $this->IgnoreRowsArrayObject($arrayTableObj,$this->ignoreRows);
        $objTableArray = $this->IgnoreColsArrayObject($resultIgnoredTableArray,$this->ignoreCols);
        return $objTableArray;
    }
    private function IgnoreRowsArrayObject($arrayTableObj,$arrayIgnoreRows){
        $tempTableObjArray = array();
        if (count($arrayIgnoreRows) > 0) {
            for ($i=0;$i<count($arrayTableObj);$i++) {
                if (!in_array($i,$arrayIgnoreRows)) {
                    $tempTableObjArray[] = $arrayTableObj[$i];
                }
            }
            return $tempTableObjArray;
        } else {
            return $arrayTableObj;
        }
    }
    private function IgnoreColsArrayObject($arrayTableObj,$arrayIgnoreCols){
        $tempTableObjArray = array();
        if (count($arrayIgnoreCols) > 0) {
            for ($i=0;$i<count($arrayTableObj);$i++) {
                for ($j=0;$j<count($arrayTableObj[$i]);$j++) {
                    if (!in_array($j,$arrayIgnoreCols)) {
                        $tempTableObjArray[$i][] = $arrayTableObj[$i][$j];
                    }
                }
            }
            return $tempTableObjArray;
        } else {
            return $arrayTableObj;
        }

        /*if (0 != count($arrayIgnoreCols)) {
            $maxColsNum = 0;
            foreach ($arrayTableObj as $row) {
                $itemNum = count($row);
                if ($itemNum>$maxColsNum) $maxColsNum = $itemNum;
            }
            $newTableArray = array();
            for ($i=0;$i<count($arrayTableObj);$i++) {
                $row = $arrayTableObj[$i];
                $newr = new TableRow();
                $newr->countCells();
                foreach ($arrayIgnoreCols as $igcol) {
                    if ($igcol>=0&&$igcol<($row->countCells())) {
                        $row->removeCell($igcol);
                    }
                }
                $newTableArray[] = $row;
            }
            return $newTableArray;
        } else {
            return $arrayTableObj;
        }*/
    }
    private function ArrayTR2ObjectArray($arrayTR) {
        /* @var $arrayTableRow TableCell[][]*/
        $arrayTableRow = array();
        $totalArrayTR = count($arrayTR);
        $totalArrayTRAfterIgnore = count($arrayTR) - count($this->ignoreRows);
        $colsNumber = $this->countCols($arrayTR);
        for ($i=0;$i<$totalArrayTR;$i++) {
            //if (in_array($i,$this->ignoreRows)) continue;
            $row = $arrayTR[$i];
            $dom = new DOMDocument();
            $dom->loadHTML(mb_convert_encoding($row, 'HTML-ENTITIES', 'UTF-8'));
            $dom->preserveWhiteSpace = false;
            $cols = $dom->getElementsByTagName('td');
            $newTableRow = array();
            for ($k=0;$k<$cols->length;$k++) {
                $existColSpan = $cols->item($k)->getAttribute('colspan');
                $existRowSpan = $cols->item($k)->getAttribute('rowspan');
                if ('' === $existColSpan) {
                    $cols->item($k)->setAttribute('colspan','1');
                }
                if ('' === $existRowSpan) {
                    $cols->item($k)->setAttribute('rowspan','1');
                }
                if ('0' === $existColSpan) {
                    $colSpan2End = $colsNumber - $k;
                    $cols->item($k)->setAttribute('colspan',$colSpan2End);
                }
                if ('0' === $existRowSpan) {
                    $rowSpan2End = $totalArrayTRAfterIgnore - $i;
                    $cols->item($k)->setAttribute('rowspan',$rowSpan2End);
                }
                if (in_array($k,$this->plainTextCols)) {
                    $cellContent = strip_tags($cols->item($k)->nodeValue);
                    $cellContent = htmlentities($cellContent);
                    $cellContent = str_ireplace('&nbsp;',' ',$cellContent);
                    $cellContent = preg_replace('!\s+!', ' ', $cellContent);
                    $cellContent = trim($cellContent);
                    $cellContent = html_entity_decode($cellContent);
                } else {
                    $cellContent = $this->get_inner_html($cols->item($k));
                    $cellContent = htmlentities($cellContent);
                    $cellContent = str_ireplace('&nbsp;',' ',$cellContent);
                    $cellContent = preg_replace('!\s+!', ' ', $cellContent);
                    $cellContent = trim($cellContent);
                    $cellContent = html_entity_decode($cellContent);
                }
                $colSpan = $cols->item($k)->getAttribute('colspan');
                $rowSpan = $cols->item($k)->getAttribute('rowspan');
                $newTableCell = new TableCell();
                $newTableCell->setCellText($cellContent);
                $newTableCell->setColumnSpan($colSpan);
                $newTableCell->setRowSpan($rowSpan);
                $newTableRow[] = $newTableCell;
            }
            $arrayTableRow[] = $newTableRow;
        }
        $maxColumnNum = 0;
        for ($i=0;$i<$totalArrayTR;$i++) {
            $j = 0;
            foreach ($arrayTableRow[$i] as $item) {
                $cs = $item->getColumnSpan();
                if ($cs > 1) {
                    $j = $j + $cs;
                } else $j++;
            }
            if ($maxColumnNum < $j) $maxColumnNum = $j;
        }
        $arrayFullTable = array();
        /* @var $arrayFullTable TableCell[][]*/
        $tableRowColSpan = false;
        $tableRowSpan = false;
        $tableColSpan = false;
        foreach ($arrayTableRow as $row) {
            foreach ($row as $cell) {
                if (1 < $cell->getRowSpan() && 1 < $cell->getColumnSpan()) {
                    $tableRowColSpan = true;
                } elseif (1 == $cell->getRowSpan() && 1 < $cell->getColumnSpan()) {
                    $tableColSpan = true;
                } elseif (1 < $cell->getRowSpan() && 1 == $cell->getColumnSpan()) {
                    $tableRowSpan = true;
                }
            }
        }
        if ($tableRowColSpan) {
            $tableType = 5; //have rowspan and have colspan in a cell
        } else {
            if ($tableColSpan && $tableRowSpan) {
                $tableType = 4; //have rowspan and have colspan but not in a cell
            } elseif (!$tableColSpan && $tableRowSpan) {
                $tableType = 3; //have rowspan and no colspan
            } elseif ($tableColSpan && !$tableRowSpan) {
                $tableType = 2; //no rowspan and have colspan
            } else {
                if ($maxColumnNum > 0 && $totalArrayTR > 0) {
                    $tableType = 1; //no rowspan and no colspan
                } else {
                    $tableType = 0;
                }
            }
        }
        $this->tableType = $tableType;
        /*
        if (1 == $tableType) {
            for ($i=0;$i<$totalArrayTR;$i++) {
                $j = 0;
                foreach ($arrayTableRow[$i] as $k=>$item) {
                    $tempCell = $arrayTableRow[$i][$k];
                    $tempCell->setId(md5(uniqid(rand())));
                    $tempCell->setPosInColSpan(1);
                    $tempCell->setColumnSpan(1);
                    $tempCell->setRowSpan(1);
                    $tempCell->setPosInRowSpan(1);
                    $tempCell->setColumnPos($j);
                    $tempCell->setRowPos($i);
                    $arrayFullTable[$i][$j] = $tempCell;
                    $j++;
                }
            }
            for ($i=0;$i<$totalArrayTR;$i++) {
                for ($j=0;$j<$maxColumnNum;$j++) {
                    if (!isset($arrayFullTable[$i][$j])) {
                        $tempCell = new TableCell();
                        $tempCell->setCellText('-');
                        $tempCell->setHasValue(true);
                        $arrayFullTable[$i][$j] = $tempCell;
                    }
                }
            }
        }
        */
        if ((5 == $tableType) || (4 == $tableType) || (3 == $tableType) || (2 == $tableType) || (1 == $tableType)) {
            for ($i=0;$i<$totalArrayTR;$i++) {
                foreach ($arrayTableRow[$i] as $item) {
                    for ($j=0;$j<$maxColumnNum;$j++) {
                        if (isset($arrayFullTable[$i][$j])) {
                            continue;
                        } else {
                            $arrayFullTable[$i][$j] = $item;
                            $sc                     = $item->getColumnSpan();
                            $sr                     = $item->getRowSpan();
                            $tempCell               = new TableCell();
                            $tempCell->setCellText('');
                            if ($sr > 1 && $sc > 1) {
                                for ($ri=0;$ri<$sr;$ri++) {
                                    for ($ci=0;$ci<$sc;$ci++) {
                                        $tempRow = $i+$ri;
                                        $tempCol = $j+$ci;
                                        if (0 == $ri && 0 == $ci) {
                                        } else {
                                            $arrayFullTable[$tempRow][$tempCol] = $tempCell;
                                        }
                                    }
                                }
                            } else {
                                if ($sr > 1) {
                                    for ($ri = 1; $ri < $sr; $ri++) {
                                        $tempRow = $i + $ri;
                                        if ($tempRow < $totalArrayTR) {
                                            $arrayFullTable[$tempRow][$j] = $tempCell;
                                        }
                                    }
                                }
                                if ($sc > 1) {
                                    for ($ci = 1; $ci < $sc; $ci++) {
                                        $tempCol = $j + $ci;
                                        if ($tempCol < $maxColumnNum) {
                                            $arrayFullTable[$i][$tempCol] = $tempCell;
                                        }
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            }
            for ($i=0;$i<$totalArrayTR;$i++) {
                for ($j=0;$j<$maxColumnNum;$j++) {
                    if (!isset($arrayFullTable[$i][$j])) {
                        $tempCell = new TableCell();
                        $tempCell->setCellText('-');
                        $tempCell->setHasValue(true);
                        $arrayFullTable[$i][$j] = $tempCell;
                    }
                }
            }
        }
        return $arrayFullTable;
    }
    private function get_inner_html( $node ) {
        $innerHTML= '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML( $child );
        }
        return $innerHTML;
    }

    private function countCols($arrayTR) {
        $maxCountTd = 0;
        foreach ($arrayTR as $tr) {
            $dom1 = new DOMDocument();
            $dom1->loadHTML($tr);
            $dom1->preserveWhiteSpace = false;
            $cols1 = $dom1->getElementsByTagName('td');
            $countTd = $cols1->length;
            if ($countTd > $maxCountTd) $maxCountTd = $countTd;
        }
        return $maxCountTd;
    }
    private function Table2ArrayTR($tableHtml) {
        $originTableHtml = $tableHtml;
        $tableHtml = str_ireplace("<th",'<td', $tableHtml);
        $tableHtml = str_ireplace("</th",'</td', $tableHtml);
        //echo '<pre>'.htmlspecialchars($tableHtml).'</pre>';
        //$length = strlen($tableHtml);
        //$rowArray = array();
        $headerArray = array();
        $bodyArray = array();
        $noBodyArray = array();
        $footerArray = array();
        //Read <thead> by <tr>
        $theadStart = stripos($tableHtml, '<thead');
        if (false != $theadStart) {
            $theadEnd = stripos($tableHtml,'</thead>',$theadStart) + strlen('</thead>');
            if (!$theadEnd) $theadEnd = stripos($tableHtml,'</thead >',$theadStart) + strlen('</thead >');
            $headerLength = ($theadEnd - $theadStart);
            $header = substr($tableHtml, $theadStart,$headerLength);
            $headerForReplace = $header;
            $headTrStart = stripos($header,'<tr');
            if ($headTrStart) {
                $startPos = stripos($header, '<tr');
                for ($j = 0; false !== $startPos; $j++) {
                    $endPos = stripos($header, '</tr>', $startPos);
                    $endPos += strlen('</tr>');
                    $length = $endPos - $startPos;
                    $temp = substr($header, $startPos, $length);
                    $headerArray[] = $temp;
                    $header = str_ireplace($temp,'',$header);
                    $header = preg_replace('!\s+!', ' ', $header);
                    $startPos = stripos($header, '<tr');
                }
            }
            $tableHtml = str_ireplace($headerForReplace,'', $tableHtml);
            $tableHtml = preg_replace('!\s+!', ' ', $tableHtml);
        }
        //Read <tfoot> by <tr>
        $tfootStart = stripos($tableHtml, '<tfoot');
        if (false != $tfootStart) {
            $tfootEnd = stripos($tableHtml,'</tfoot>',$tfootStart) + strlen('</tfoot>');
            if (!$tfootEnd) $tfootEnd = stripos($tableHtml,'</tfoot >',$tfootStart) + strlen('</tfoot >');
            $footerLength = ($tfootEnd - $tfootStart);
            $footer = substr($tableHtml, $tfootStart,$footerLength);
            $footerForReplace = $footer;
            $footTrStart = stripos($footer,'<tr');
            if ($footTrStart) {
                $startPos = stripos($footer, '<tr');
                for ($j = 0; false !== $startPos; $j++) {
                    $endPos = stripos($footer, '</tr>', $startPos);
                    $endPos += strlen('</tr>');
                    $length = $endPos - $startPos;
                    $temp = substr($footer, $startPos, $length);
                    $footerArray[] = $temp;
                    $footer = str_ireplace($temp,'',$footer);
                    $footer = preg_replace('!\s+!', ' ', $footer);
                    $startPos = stripos($footer, '<tr');
                }
            }
            $tableHtml = str_ireplace($footerForReplace,'', $tableHtml);
            $tableHtml = preg_replace('!\s+!', ' ', $tableHtml);
        }
        //Read <tbody> by <tr>
        $tbodyStart = stripos($tableHtml, '<tbody');
        if (false != $tbodyStart) {
            $tbodyEnd = stripos($tableHtml,'</tbody>',$tbodyStart) + strlen('</tbody>');
            if (!$tbodyEnd) $tbodyEnd = stripos($tableHtml,'</tbody >',$tbodyStart) + strlen('</tbody >');
            $bodyLength = ($tbodyEnd - $tbodyStart);
            $body = substr($tableHtml, $tbodyStart, $bodyLength);
            $bodyForReplace = $body;
            $bodyTrStart = stripos($body,'<tr');
            if ($bodyTrStart) {
                $startPos = stripos($body, '<tr');
                for ($j = 0; false !== $startPos; $j++) {
                    $endPos = stripos($body, '</tr>', $startPos);
                    $endPos += strlen('</tr>');
                    $length = $endPos - $startPos;
                    $temp = substr($body, $startPos, $length);
                    $bodyArray[] = $temp;
                    $body = str_ireplace($temp,'',$body);
                    $body = preg_replace('!\s+!', ' ', $body);
                    $startPos = stripos($body, '<tr');
                }
            }
            $tableHtml = str_ireplace($bodyForReplace,'', $tableHtml);
            $tableHtml = preg_replace('!\s+!', ' ', $tableHtml);
        }

        //Read each <tr> without <tbody>
        $tableForReplace = $tableHtml;
        $noBodyTrStart = stripos($tableHtml,'<tr');
        if ($noBodyTrStart) {
            $startPos = stripos($tableHtml, '<tr');
            for ($j = 0; false !== $startPos; $j++) {
                $endPos = stripos($tableHtml, '</tr>', $startPos);
                $endPos += strlen('</tr>');
                $length = $endPos - $startPos;
                $temp = substr($tableHtml, $startPos, $length);
                $noBodyArray[] = $temp;
                $tableHtml = str_ireplace($temp,'',$tableHtml);
                $tableHtml = preg_replace('!\s+!', ' ', $tableHtml);
                $startPos = stripos($tableHtml, '<tr');
            }
        }
        $tableHtml = str_ireplace($tableForReplace,'', $tableHtml);
        //Clear $tableHtml equal null
        $tableHtml = preg_replace('!\s+!', ' ', $tableHtml);

        //echo '<pre>'.htmlspecialchars($tableHtml).'</pre>';

        if ($this->headerRowIsData) {
            $tableArray = array_merge($headerArray,$bodyArray,$noBodyArray,$footerArray);
        } else {
            $tableArray = array_merge($bodyArray,$noBodyArray,$footerArray);
        }
        return $tableArray;
    }

    /**
     * @return array
     */
    public function getPlainTextCols() {
        return $this->plainTextCols;
    }


    /**
     * @param array $plainTextCols
     */
    public function setPlainTextCols($plainTextCols) {
        $this->plainTextCols = $plainTextCols;
    }


    /**
     * @return array
     */
    public function getIgnoreCols() {
        return $this->ignoreCols;
    }


    /**
     * @param array $ignoreCols
     */
    public function setIgnoreCols($ignoreCols) {
        $this->ignoreCols = $ignoreCols;
    }


    /**
     * @return array
     */
    public function getIgnoreRows() {
        return $this->ignoreRows;
    }


    /**
     * @param array $ignoreRows
     */
    public function setIgnoreRows($ignoreRows) {
        $this->ignoreRows = $ignoreRows;
    }


    /**
     * @return bool
     */
    public function isHeaderRowIsData() {
        return $this->headerRowIsData;
    }


    /**
     * @param bool $headerRowIsData
     */
    public function setHeaderRowIsData($headerRowIsData) {
        $this->headerRowIsData = $headerRowIsData;
    }

}
