<?php

class Hackathon_AsyncIndex_Block_Adminhtml_Process_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $id = $row->getId();
        $code = $row->getData($this->getColumn()->getIndex());
        $status = $row->getData('status');

        $reindex_url = $this->getUrl('asyncindex/asyncindex/reindexProcess');
        $schedule_url = $this->getUrl('asyncindex/asyncindex/scheduleReindex');
        $partial_url = $this->getUrl('asyncindex/asyncindex/schedulePartial');
        
        if ($status === 'working') {
            $html = '<span style="color: red;">Index already running.</span>';
        } else {
            $html  = '<select class="select" style="margin-right: 7px;">';
            $html .= '<option></option>';
            $html .= '<option value="' . $reindex_url  . '">' . Mage::helper('index')->__('Reindex Data') . '</option>';
            $html .= '<option value="' . $schedule_url . '">' . Mage::helper('index')->__('Schedule Reindex') . '</option>';
            $html .= '<option value="' . $partial_url  . '">' . Mage::helper('index')->__('Schedule Partial Reindex') . '</option>';
            $html .= '</select>';
            $html .= '<button type="button" class="scalable" onclick="executeProcessTask(this, \'' . $id . '\', \'' . $code . '\')" ><span>ok</span></button>';
        }

        return $html;
    }
}
