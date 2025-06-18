<?php

namespace App\Libraries;

class ExportExcelChunk
{

    public $data;
    public $columns;
    public $fields;
    public $title;
    public $footer;
    public $row;
    public $col;
    public $filename;
    public $groups;
    public $summary;
    public $level;
    public $thead;
    public $groupTpl;

    protected $chunkSize = null;
    public $isUsingChunk = false;

    public function setChunkSize($size)
    {
        $this->chunkSize = $size;
        return $this;
    }


    public function __construct($param = array())
    {
        foreach ($param as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        $this->level       = 0;
        $this->thead       = array();
        $this->rangeSum    = array();
        $this->fields      = array();

        $this->setFields();
        $this->setHeader();

        $this->groupTpl    = isset($this->groups) ? $this->createGroupTpl() : null;
    }

    public static function isUsingChunking($params)
    {
        return isset($params['chunkSize']) && is_numeric($params['chunkSize']) && $params['chunkSize'] > 0;
    }


    public static function export($params)
    {
        $excel = new static($params); // atau new self($params)
        return $excel->run(); // PENTING! Harus return run()
    }
    public function xmlStyle()
    {
        echo '<Styles>';

        echo '<Style ss:ID="h1"><Font ss:Size="24" ss:Bold="1"/> <Alignment ss:Horizontal="Left" ss:Vertical="Center"/></Style>
              <Style ss:ID="h2"><Font ss:Size="20" ss:Bold="1"/> <Alignment ss:Horizontal="Left" ss:Vertical="Center"/></Style>
              <Style ss:ID="h3"><Font ss:Size="16" ss:Bold="1"/> <Alignment ss:Horizontal="Left" ss:Vertical="Center"/></Style>
              <Style ss:ID="h4"><Font ss:Size="14" ss:Bold="1"/> <Alignment ss:Horizontal="Left" ss:Vertical="Center"/></Style>
              <Style ss:ID="h5"><Font ss:Size="12" ss:Bold="1"/> <Alignment ss:Horizontal="Left" ss:Vertical="Center"/></Style>
              <Style ss:ID="h6"><Font ss:Size="11" ss:Bold="1"/> <Alignment ss:Horizontal="Left" ss:Vertical="Center"/></Style>
              <Style ss:ID="h7"><Font ss:Size="10" ss:Bold="1"/> <Alignment ss:Horizontal="Left" ss:Vertical="Center"/></Style>
              ';

        echo '<Style ss:ID="footer"><Alignment ss:Horizontal="Left"/><Font ss:Italic="1" ss:Size="8" /></Style>';

        echo '<Style ss:ID="thead">
                    <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
                    <Font ss:Bold="1" ss:FontName="Calibri"/>
                    <Interior ss:Color="#CCCCCC" ss:Pattern="Solid"/>
                    <Borders>
                        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                    </Borders>
              </Style>';

        echo '<Style ss:ID="tgroup">
                    <Alignment ss:Vertical="Center" ss:Horizontal="Left"/>
                    <Borders>
                        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                    </Borders>
                    <Font ss:Bold="1" ss:FontName="Calibri"/>
                    <Interior ss:Color="#EEEEEE" ss:Pattern="Solid"/>
              </Style>';

        $styles = array(
            (object)array("type" => "string", "format" => null, "align" => array("Left", "Right", "Center")),
            (object)array("type" => "int", "format" => '#,##0_ ;\-#,##0', "align" => array("Left", "Right", "Center")),
            (object)array("type" => "float", "format" => '_-* #,##0.00_-;\-* #,##0.00_-;_-* &quot;-&quot;_-;_-@_-', "align" => array("Left", "Right", "Center")),
            (object)array("type" => "date", "format" => 'dd/mm/yyyy', "align" => array("Left", "Right", "Center")),
            (object)array("type" => "time", "format" => 'hh\:mm', "align" => array("Left", "Right", "Center")),
            (object)array("type" => "datetime", "format" => 'dd/mm/yyyy\ hh\:mm', "align" => array("Left", "Right", "Center"))
        );

        foreach ($styles as $style) {
            $format = $style->format;
            $format = ($format) ? $format = '<NumberFormat ss:Format="' . $format . '"/>' : '';
            foreach ($style->align as $align) {
                echo '<Style ss:ID="tsummary-' . $style->type . '-' . strtolower($align) . '">
                            <Alignment ss:Vertical="Center" ss:Horizontal="' . $align . '"/>
                            <Borders>
                                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                            </Borders>
                            <Font ss:Bold="1" ss:FontName="Calibri"/>
                            <Interior ss:Color="#DDDDDD" ss:Pattern="Solid"/>
                            ' . $format . '
                      </Style>';

                echo '<Style ss:ID="tbody-' . $style->type . '-' . strtolower($align) . '">
                            <Alignment ss:Vertical="Top" ss:Horizontal="' . $align . '"/>
                            <Font ss:FontName="Calibri"/>
                            <Borders>
                                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#666666"/>
                            </Borders>
                            ' . $format . '
                      </Style>';

                echo '<Style ss:ID="tfoot-' . $style->type . '-' . strtolower($align) . '">
                            <Alignment ss:Vertical="Center" ss:Horizontal="' . $align . '"/>
                            <Font ss:Bold="1" ss:Color="#000000" ss:FontName="Calibri"/>
                            <Interior ss:Color="#DDDDDD" ss:Pattern="Solid"/>
                            <Borders>
                                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                            </Borders>
                            ' . $format . '
                      </Style>';
            }
        }

        echo '</Styles>';
    }

    public function sformat($tpl, $data)
    {
        $data = (object) $data;
        $result = '';
        for ($i = 0; $i < strlen($tpl); $i++) {
            $chr = substr($tpl, $i, 1);
            if ($chr == '{') {
                $j = strpos($tpl, '}', ++$i);
                $field = substr($tpl, $i, ($j - $i));
                if (isset($data->$field)) $result .= $data->$field;
                $i = $j;
            } else $result .= $chr;
        }
        return $result;
    }

    public function setFields($columns = null, $level = 0)
    {
        if (!$columns) $columns = $this->columns;
        if ($columns) {
            foreach ($columns as $column) {
                $column = (object) $column;
                if (isset($column->columns) && $column->columns && count($column->columns)) {
                    $this->setFields($column->columns, ($level + 1));
                } else {
                    $align   = (isset($column->align)) ? $column->align : null;
                    $type    = (isset($column->type)) ? strtolower($column->type) : 'string';
                    $formula = (isset($column->formula)) ? $column->formula : null;

                    if ($type == 'int' || $type == 'integer' || $type == 'number') $type = 'int';
                    else if ($type == 'float' || $type == 'double') $type = 'float';

                    if (!$align) {
                        if ($type == 'int' || $type == 'float') $align = 'right';
                        else if ($type == 'date' || $type == 'datetime' || $type == 'time') $align = 'center';
                        else $align = 'left';
                    }

                    array_push($this->fields, (object) array(
                        'name'    => (isset($column->dataIndex)) ? $column->dataIndex : null,
                        'formula' => $formula,
                        'type'    => $type,
                        'align'   => $align,
                        'width'   => (isset($column->width)) ? $column->width : 100,
                        'summary' => (isset($column->summaryType)) ? $column->summaryType : null,
                        'renderer' => (isset($column->renderer)) ? $column->renderer : null,
                    ));
                }
                if (isset($column->summaryType) && $column->summaryType) $this->summary = true;
            }
        }
        if ($this->level < $level) $this->level = $level;
    }

    public function getColspan($columns, $count = 0)
    {
        $count--;
        foreach ($columns as $column) {
            $column = (object) $column;
            if (isset($column->columns) && $column->columns && count($column->columns)) {
                $count += $this->getColspan($column->columns, count($column->columns));
            }
        }
        return $count;
    }

    public function setHeader($columns = null, $level = 0, $cellIndex = 0)
    {
        if (!$columns) $columns = $this->columns;

        if ($columns) {
            foreach ($columns as $column) {
                $column = (object) $column;
                if (isset($column->columns) && $column->columns && count($column->columns)) {
                    $colspan = $this->getColspan($column->columns, count($column->columns));

                    $head = (object) array(
                        'text'       => (isset($column->text)) ? $column->text : '',
                        'colspan'    => $colspan,
                        'rowspan'    => 0,
                        'cellIndex'  => $cellIndex + 1
                    );
                    if (isset($this->thead[$level])) array_push($this->thead[$level], $head);
                    else $this->thead[$level] = array($head);

                    $this->setHeader($column->columns, ($level + 1), $cellIndex);
                    $cellIndex = $cellIndex + $colspan;
                } else {
                    $head = (object) array(
                        'text'       => (isset($column->text)) ? str_replace('<br>', '&#10;', $column->text) : '',
                        'colspan'    => 0,
                        'rowspan'    => $this->level - $level,
                        'cellIndex'  => $cellIndex + 1
                    );
                    if (isset($this->thead[$level])) array_push($this->thead[$level], $head);
                    else $this->thead[$level] = array($head);
                }


                $cellIndex++;
            }
        }
    }

    public function setColumnWidth()
    {
        foreach ($this->fields as $row) {
            $width = 85;
            if (isset($row->width) && $row->width) $width = $row->width * 0.7;
            echo '<Column ss:AutoFitWidth="0" ss:Width="' . $width . '"/>';
        }
    }

    public function createGroupTpl()
    {
        $template = $this->groups->template;
        $result   = array();
        $text     = '';
        for ($i = 0; $i < strlen($template); $i++) {
            $chr = substr($template, $i, 1);
            if ($chr == '{' || $chr == '}' || $i == (strlen($template) - 1)) {
                if ($chr == '}') {
                    $field = null;
                    foreach ($this->fields as $col) {
                        if ($col->name == $text) {
                            $field = $col;
                            break;
                        }
                    }
                    array_push($result, array($text, 'data', $field));
                } else {
                    if ($chr == '{') array_push($result, array($text, 'string', null));
                    else array_push($result, array($text . $chr, 'string', null));
                }
                $text = '';
            } else $text .= $chr;
        }
        return $result;
    }

    public function startXML()
    {
        echo '<?xml version="1.0" encoding="UTF-8"?>
               <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">';
        $this->xmlStyle();
        echo '<Worksheet ss:Name="Sheet1">';
        echo '<Table>';
    }

    public function endXML()
    {
        echo '</Table>';
        echo '</Worksheet>';
        echo '</Workbook>';
    }

    public function showTitle()
    {
        if ($title = $this->title) {
            if (gettype($title) == 'array') {
                for ($i = 0; $i < count($title); $i++) {
                    $data = $title[$i];
                    if (gettype($data) == 'array') {
                        $text  = $data[0];
                        $style = $data[1];
                    } else {
                        $text = $data;
                        $style = 'h1';
                    }
                    echo '<Row ss:AutoFitHeight="1"><Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $text . '</Data></Cell></Row>';
                }
            } else echo '<Row ss:AutoFitHeight="1"><Cell ss:StyleID="h1"><Data ss:Type="String">' . $title . '</Data></Cell></Row>';
            echo '<Row><Cell></Cell></Row>';
        }
    }

    public function showHeader()
    {
        foreach ($this->thead as $row) {
            $index = 1;
            echo '<Row ss:AutoFitHeight="0" ss:Height="25">';
            foreach ($row as $data) {
                if ($index == $data->cellIndex) {
                    if ($data->colspan) {
                        echo '<Cell ss:MergeAcross="' . $data->colspan . '" ss:StyleID="thead"><Data ss:Type="String">' . $data->text . '</Data></Cell>';
                    } else if ($data->rowspan) echo '<Cell ss:MergeDown="' . $data->rowspan . '" ss:StyleID="thead"><Data ss:Type="String">' . $data->text . '</Data></Cell>';
                    else echo '<Cell ss:StyleID="thead"><Data ss:Type="String">' . $data->text . '</Data></Cell>';
                } else {
                    if ($data->colspan) {
                        echo '<Cell ss:Index="' . $data->cellIndex . '" ss:MergeAcross="' . $data->colspan . '" ss:StyleID="thead"><Data ss:Type="String">' . $data->text . '</Data></Cell>';
                    } else if ($data->rowspan) echo '<Cell ss:Index="' . $data->cellIndex . '" ss:MergeDown="' . $data->rowspan . '" ss:StyleID="thead"><Data ss:Type="String">' . $data->text . '</Data></Cell>';
                    else echo '<Cell ss:Index="' . $data->cellIndex . '" ss:StyleID="thead"><Data ss:Type="String">' . $data->text . '</Data></Cell>';
                    $index = $data->cellIndex;
                }
                $index++;
            }
            echo '</Row>';
        }
    }

    public function showGroup($index, $countFields)
    {
        $data    = $this->data;
        $groupId = $this->groups->id;
        $row     = $data[$index];

        if ($index) $next = $data[$index - 1];

        if (!$index || $row->$groupId != $next->$groupId) {
            $text = '';
            for ($i = 0; $i < count($this->groupTpl); $i++) {
                $r      = $this->groupTpl[$i];
                $txt    = $r[0];
                $isData = ($r[1] == 'data') ? true : false;
                $field  = $r[2];

                if ($isData) {
                    if ($field) {
                        $name = $field->name;
                        $txt  = $row->$name;
                        switch ($field->type) {
                            case "int":
                                $txt = number_format($txt, 0, ".", ",");
                                break;
                            case "float":
                                $txt = number_format($txt, 2, ".", ",");
                                break;
                            case "date":
                                $txt = $txt ? date('d/m/Y', strtotime($txt)) : '';
                                break;
                            case "datetime":
                                $txt = $txt ? date('d/m/Y H:i', strtotime($txt)) : '';
                                break;
                            case "time":
                                $txt = $txt ? date('H:i:s', strtotime($txt)) : '';
                                break;
                        }
                        $text .= $txt;
                    } else $text .= $row->$txt;
                } else $text .= $txt;
            }
            echo '<Row ss:Height="21" ss:AutoFitHeight="0">';
            echo '<Cell ss:MergeAcross="' . ($countFields - 1) . '" ss:StyleID="tgroup"><Data ss:Type="String">' . $text . '</Data></Cell>';
            echo '</Row>';
            return $index++;
        }

        return false;
    }

    public function showGroupSummary($index, $rowCount, $R)
    {
        $data    = $this->data;
        $groupId = $this->groups->id;
        $row     = $data[$index];

        if ($index < ($rowCount - 1)) $next = $data[$index + 1];

        if (($index && $index == ($rowCount - 1)) || ($row->$groupId != $next->$groupId)) {
            echo '<Row>';
            $num = 0;
            foreach ($this->fields as $field) {
                $formula = null;
                $range   = ($R > 1) ? "R[-$R]C:R[-1]C" : "R[-1]C";

                if (strtolower($field->summary) == 'sum') $formula = "=SUM($range)";
                else if (strtolower($field->summary) == 'avg' || strtolower($field->summary) == 'average') $formula = "=AVERAGE($range)";
                else if (strtolower($field->summary) == 'count') $formula = "=CONCATENATE(&quot;(&quot;,COUNTA($range),&quot;) Row&quot;)";

                $style = "tsummary-$field->type-$field->align";
                if ($field->summary) {
                    if ($formula) echo '<Cell ss:StyleID="' . $style . '" ss:Formula="' . $formula . '"><Data ss:Type="Number">0</Data></Cell>';
                    else echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $field->summary . '</Data></Cell>';
                } else echo '<Cell ss:StyleID="tsummary-string-left"><Data ss:Type="String"></Data></Cell>';
                $num++;
            }
            echo '</Row>';
            return true;
        }

        return false;
    }

    public function showSummary($range, $count, $rows)
    {
        $groups   = $this->groups;
        $sum      = '';

        if ($range && count($range) && $groups) {
            $const = (isset($groups->summary) && $groups->summary) ? 2 : 1;
            for ($i = 0; $i < count($range); $i++) {
                $min = ($range[$i] + 1) - $rows;
                $max = ($i < (count($range) - 1)) ? $range[$i + 1] : $rows;
                $max = ($max - $const) - $rows;
                if ($i) $sum .= ',';
                $sum .= "R[$min]C:R[$max]C";
            }
        } else {
            $min = -1;
            $max = 1 - $rows;
            $sum = "R[$min]C:R[$max]C";
        }

        echo '<Row ss:Height="21" ss:AutoFitHeight="0">';
        foreach ($this->fields as $field) {
            $formula = '';
            if (strtolower($field->summary) == 'sum') $formula = "=SUM($sum)";
            else if (strtolower($field->summary) == 'avg' || strtolower($field->summary) == 'average') $formula = "=AVERAGE($sum)";
            else if (strtolower($field->summary) == 'count') $formula = "=CONCATENATE(&quot;(&quot;,COUNTA($sum),&quot;) Row&quot;)";

            $style = "tfoot-$field->type-$field->align";
            if ($field->summary) {
                if ($formula) echo '<Cell ss:StyleID="' . $style . '" ss:Formula="' . $formula . '"><Data ss:Type="Number">0</Data></Cell>';
                else echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . $field->summary . '</Data></Cell>';
            } else echo '<Cell ss:StyleID="tfoot-string-left"><Data ss:Type="String"></Data></Cell>';
        }
        echo '</Row>';
    }

    public function showData()
    {
        $groups      = $this->groups;
        $idgroup     = null;
        $countFields = count($this->fields);
        $countRow    = count($this->data);
        $index       = 0;
        $startGroup  = 0;
        $rows        = 0;
        $rangeSum    = array();
        $gsummary    = array();

        foreach ($this->data as $row) {
            if ($groups) {
                $isGroup = $this->showGroup($index, $countFields);
                if ($isGroup || !$index) {
                    $rows++;
                    array_push($rangeSum, $rows);
                }
                if (!$isGroup) {
                    $startGroup++;
                }
            }

            echo '<Row>';
            foreach ($this->fields as $col) {
                $dataIndex = $col->name;
                $text = (isset($row->$dataIndex)) ? $row->$dataIndex : '';
                if (is_callable($renderer = $col->renderer)) {
                    $text = $renderer($text, $row);
                } else {
                    $text = str_replace("'", ' ', $text);
                    $text = str_replace('"', ' ', $text);
                    $text = str_replace("`", ' ', $text);
                    $text = str_replace("<", ' ', $text);
                    $text = str_replace(">", ' ', $text);
                    $text = str_replace(";", ' ', $text);
                }
                switch ($col->type) {
                    case "int":
                        $dataType = "Number";
                        break;
                    case "float":
                        $dataType = "Number";
                        break;
                    case "date":
                        $dataType = "DateTime";
                        $text = str_replace(' ', 'T', $text);
                        break;
                    case "datetime":
                        $dataType = "DateTime";
                        $text = str_replace(' ', 'T', $text);
                        break;
                    case "time":
                        $dataType = "DateTime";
                        break;
                    default:
                        $dataType = "String";
                }
                $style = "tbody-$col->type-$col->align";
                if ($col->formula) {
                    $col->formula = str_replace("'", '&quot;', $col->formula);
                    $col->formula = str_replace('"', '&quot;', $col->formula);
                    echo '<Cell ss:StyleID="' . $style . '" ss:Formula="' . $this->sformat($col->formula, $row) . '"><Data ss:Type="' . $dataType . '">' . $text . '</Data></Cell>';
                } else echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="' . $dataType . '">' . $text . '</Data></Cell>';
            }
            echo '</Row>';
            $rows++;

            if (isset($groups->summary) && $groups->summary) {
                $isSummary = $this->showGroupSummary($index, $countRow, $startGroup);
                if ($isSummary) {
                    $startGroup = 1;
                    $rows++;
                }
            }

            $index++;
        }

        if ($this->summary && $countRow) $this->showSummary($rangeSum, $countRow, ++$rows);
    }

    public function showFooter()
    {
        if ($footer = $this->footer) {
            echo '<Row><Cell></Cell></Row>';
            if (gettype($footer) == 'array') {
                for ($i = 0; $i < count($footer); $i++) {
                    echo '<Row ss:AutoFitHeight="1"><Cell ss:StyleID="footer"><Data ss:Type="String">' . $footer[$i] . '</Data></Cell></Row>';
                }
            } else echo '<Row ss:AutoFitHeight="1"><Cell ss:StyleID="footer"><Data ss:Type="String">' . $footer . '</Data></Cell></Row>';
        }
    }

    public function renderChunk($chunkSize = 5000)
    {
        $this->isUsingChunk = true;

        $data      = collect($this->data)->all();
        $total     = count($data);
        $offset    = 0;
        $rowIndex  = 0;

        $count     = count($data);
        $rangeSum  = [];
        $rows      = 0;
        $groups    = $this->groups;
        $fields    = $this->fields;
        $countFields = count($fields);

        while ($offset < $total) {
            $chunk = array_slice($data, $offset, $chunkSize);
            $startGroup = 0;

            foreach ($chunk as $index => $row) {
                $realIndex = $offset + $index;

                if ($groups) {
                    $isGroup = $this->showGroup($realIndex, $countFields);
                    if ($isGroup || !$realIndex) {
                        $rows++;
                        $rangeSum[] = $rows;
                    }
                }

                echo '<Row>';
                foreach ($fields as $col) {
                    $dataIndex = $col->name;
                    $text = (isset($row->$dataIndex)) ? $row->$dataIndex : '';

                    if (is_callable($renderer = $col->renderer)) {
                        $text = $renderer($text, $row);
                    } else {
                        $text = str_replace(["'", '"', '`', '<', '>', ';'], ' ', $text);
                    }

                    switch ($col->type) {
                        case "int":
                        case "float":
                            $dataType = "Number";
                            break;
                        case "date":
                        case "datetime":
                            $dataType = "DateTime";
                            $text = str_replace(' ', 'T', $text);
                            break;
                        case "time":
                            $dataType = "DateTime";
                            break;
                        default:
                            $dataType = "String";
                    }

                    $style = "tbody-$col->type-$col->align";

                    if ($col->formula) {
                        $col->formula = str_replace(["'", '"'], '&quot;', $col->formula);
                        echo '<Cell ss:StyleID="' . $style . '" ss:Formula="' . $this->sformat($col->formula, $row) . '"><Data ss:Type="' . $dataType . '">' . $text . '</Data></Cell>';
                    } else {
                        echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="' . $dataType . '">' . $text . '</Data></Cell>';
                    }
                }
                echo '</Row>';
                $rows++;

                if (isset($groups->summary) && $groups->summary) {
                    $isSummary = $this->showGroupSummary($realIndex, $total, $startGroup);
                    if ($isSummary) {
                        $startGroup = 1;
                        $rows++;
                    }
                }
            }

            $offset += $chunkSize;
        }

        if ($this->summary && $total) {
            $this->showSummary($rangeSum, $total, ++$rows);
        }
    }



    public function run()
    {
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: inline; filename="' . $this->filename . '.xls"');

        if (ob_get_length() > 0) ob_end_clean();

        $this->startXML();
        $this->setColumnWidth();
        $this->showTitle();
        $this->showHeader();
        if (!empty($this->chunkSize) && is_numeric($this->chunkSize) && $this->chunkSize > 0) {
            $this->renderChunk($this->chunkSize);
        } else {
            $this->showData();
        }
        $this->showFooter();
        $this->endXML();

        return $this->isUsingChunk;
    }
}
