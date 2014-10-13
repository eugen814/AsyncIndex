<?php

class Hackathon_AsyncIndex_Block_Adminhtml_Process_Grid extends Mage_Index_Block_Adminhtml_Process_Grid
{

    protected function _afterLoadCollection()
    {
        parent::_afterLoadCollection();
        /** @var $process Mage_Index_Model_Process */
        foreach ($this->_collection as $process) {
            $process->setEventCount($process->getUnprocessedEventsCollection()->count());
        }
        return $this;
    }

    /**
     * Replace old Admin-Grid with shiny new one
     * @return Mage_Index_Block_Adminhtml_Process_Grid|void
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        //Drop the old button
        $this->removeColumn('action');

        //Count of unindexed Data
        $this->addColumn('event_count', array(
                'header'   => Mage::helper('index')->__('Event Count'),
                'width'    => '80',
                'index'    => 'event_count',
                'sortable' => false
            )
        );

        //Reindex-Queue-Button
        $this->addColumn('action', array(
                'header'    => Mage::helper('index')->__('Action'),
                'align'     => 'left',
                'width'     => '220',
                'index'     => 'indexer_code',
                'renderer'  => 'Hackathon_AsyncIndex_Block_Adminhtml_Process_Renderer_Actions',
                // 'type'      => 'action',
                // 'getter'    => 'getId',
                // 'actions'   => array(
                    // array(
                        // 'caption' => Mage::helper('index')->__('Reindex Data'),
                        // 'url'     => array('base' => '*/*/reindexProcess'),
                        // 'field'   => 'process'
                    // ),
                    // array(
                        // 'caption' => Mage::helper('index')->__('Schedule Reindex'),
                        // 'url'     => array('base' => 'asyncindex/asyncindex/index'),
                        // 'params'  => array('_current' => true, '_secure' => false),
                        // 'field'   => 'process'
                    // ),
                    // array(
                        // 'caption' => Mage::helper('index')->__('Schedule partial index'),
                        // 'url'     => array('base' => 'asyncindex/asyncindex/schedulePartial'),
                        // 'params'  => array('_current' => true, '_secure' => false),
                        // 'field'   => 'process'
                    // ),
                // ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            )
        );
    }
}
