<?php

class Hackathon_AsyncIndex_AsyncindexController extends Mage_Adminhtml_Controller_Action {

    /**
     * reindex selected process
     */
    public function reindexProcessAction() {
        /** @var $process Mage_Index_Model_Process */
        $process = $this->_initProcess();
        if ($process) {
            try {
                Varien_Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');
                $process->reindexEverything();
                Varien_Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
                $resp = Mage::helper('index')->__('%s index was rebuilt.', $process->getIndexer()->getName());
                $this->getResponse()->setBody($resp);
            } catch (Mage_Core_Exception $e) {
                $this->getResponse()->setBody($e->getMessage());
            } catch (Exception $e) {
                $this->getResponse()->setBody(Mage::helper('index')->__('There was a problem with reindexing process.'));
            }
        } else {
            $this->getResponse()->setBody(Mage::helper('index')->__('Cannot initialize the indexer process.'));
        }
    }


    /**
     * schedule normal reindex in crontab
     */
    public function scheduleReindexAction() {
        $process = $this->getProcessCodeFromRequestParams(); 
        $resp = $this->tryScheduleIndex($process, true);
        $this->getResponse()->setBody($resp);
    }


    /**
     * schedule partial reindex in crontab
     */
    public function schedulePartialAction() {
        $process = $this->getProcessCodeFromRequestParams();
        $resp = $this->tryScheduleIndex($process);
        $this->getResponse()->setBody($resp);
    }


    /**
     * get the process code/id from the request and return just the string
     * @return string
     */
    protected function getProcessCodeFromRequestParams() {
        $process   = $this->getRequest()->getParam('process_code');
        $processId = $this->getRequest()->getParam('process');
        if ($processId) {
            $processModel = Mage::getModel('index/process');
            $processModel->load($processId);
            $process = $processModel->getIndexerCode();
        }
        return $process;
    }


    /**
     * add index to crontab
     * @param string $indexerCode process code of the indexer
     * @param bool $fullReindex should we do a full reindex?
     */
    protected function tryScheduleIndex( $indexerCode, $fullReindex = false ) {
        /** @var Mage_Adminhtml_Model_Session $session */
        $session = Mage::getSingleton('adminhtml/session');
        $helper  = Mage::helper('core');
        $message = array(
            "indexerCode" => $indexerCode,
            "fullReindex" => $fullReindex,
        );
        $taskName = $fullReindex ? 'Reindex' : 'partial Index';
        try {
            /** @var Mage_Cron_Model_Schedule $schedule */
            $schedule = Mage::getModel('cron/schedule');
            $schedule->setJobCode('hackathon_asyncindex_cron');
            $schedule->setCreatedAt(date('Y-m-d H:i:s'));
            $schedule->setMessages(json_encode($message));
            $schedule->setScheduledAt(date('Y-m-d H:i:s'));
            $schedule->save();
            return $helper->__($taskName.' successfully scheduled for process ') . $indexerCode;
        } catch (Exception $e) {
            return $helper->__($taskName.' schedule not successful, message: %s', $e->getMessage());
        }
    }


    /**
     * @return object or bool false
     */
    protected function _initProcess() {
        $processId = $this->getRequest()->getParam('process');
        if ($processId) {
            /** @var $process Mage_Index_Model_Process */
            $process = Mage::getModel('index/process')->load($processId);
            if ($process->getId() && $process->getIndexer()->isVisible()) {
                return $process;
            }
        }
        return false;
    }


    protected function _isAllowed() {
        return true;
    }

}
