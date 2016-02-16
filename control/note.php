<?php

!defined('IN_TIPASK') && exit('Access Denied');

class notecontrol extends base {

    function notecontrol(& $get, & $post) {
        $this->base($get, $post);
        $this->load("note");
        $this->load("note_comment");
    }

    /* ǰ̨�鿴�����б� */

    function onlist() {
        $navtitle = '�����б�';
        $page = max(1, intval($this->get[2]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $rownum = $this->db->fetch_total('note', ' 1=1');
        $notelist = $_ENV['note']->get_list($startindex, $pagesize);
        $departstr = page($rownum, $pagesize, $page, "note/list");
        include template('notelist');
    }

    /* ������� */

    function onview() {
        $navtitle = '�鿴����';
        $page = max(1, intval($this->get[3]));
        $pagesize = $this->setting['list_default'];
        $startindex = ($page - 1) * $pagesize;
        $rownum = $this->db->fetch_total('note', ' 1=1');
        $note = $_ENV['note']->get($this->get[2]);
        $rownum = $this->db->fetch_total('note_comment', " noteid=" . $note['id']);
        $commentlist = $_ENV['note_comment']->get_by_noteid($note['id'], $startindex, $pagesize);
        $departstr = page($rownum, $pagesize, $page, "note/view/" . $note['id']);
        $_ENV['note']->update_views($note['id']);
        $seo_title = $note['title'].' - '.$navtitle.' - '.$this->setting['site_name'];
        $seo_description = $seo_title;
        $seo_keywords = $note['title'];
        include template('note');
    }

    function onaddcomment() {
        if (isset($this->post['submit'])) {
            $noteid = intval($this->post['noteid']);
            $_ENV['note_comment']->add($noteid, $this->post['content']);
            $_ENV['note']->update_comments($noteid);
            $this->message("�������ӳɹ�!", "note/view/" . $noteid);
        }
    }

    function ondeletecomment() {
        $commentid = intval($this->get[3]);
        $noteid = intval($this->get[2]);
        $_ENV['note_comment']->remove($commentid, $noteid);
        $this->message("����ɾ���ɹ�", "note/view/$noteid");
    }

}

?>