<?php
// p2 �[�X�^�C���ݒ�
// for �A�N�e�B�u���i�[
// vim: set fileencoding=cp932 ai et ts=4 sw=0 sts=0 fdm=marker:
// mi: charset=Shift_JIS

echo <<<EOSTYLE

/* �X�C�b�` */
span.aMonaSW {
	cursor: pointer;
}

/* �A�N�e�B�u���i�[:����AA�X�^�C���K�p */
.AutoMona {
	font-family: "{$_exconf['aMona']['aafont']}";
	font-size: {$_exconf['aMona']['auto_fontsize']};
	line-height: 100%;
	white-space: nowrap;
}

/* �A�N�e�B�u���i�[:AA�X�^�C���K�p */
.ActiveMona {
	font-family: "{$_exconf['aMona']['aafont']}";
	line-height: 100%;
	white-space: nowrap;
}

/* �A�N�e�B�u���i�[:���� */
.NoMona {
	font-family: "{$STYLE['fontfamily']}";
	font-size: {$STYLE['read_fontsize']};
	line-height: 130%;
	white-space: normal;
}

/* �A�N�e�B�u���i�[:����(���X�|�b�v�A�b�v) */
.NoMonaQ {
	font-family: "{$STYLE['fontfamily']}";
	font-size: {$STYLE['respop_fontsize']};
	line-height: 120%;
	white-space: normal;
}

EOSTYLE;

// �X�^�C���̏㏑��
if (isset($MYSTYLE) && is_array($MYSTYLE)) {
	include_once (P2_STYLE_DIR . '/mystyle_css.php');
	$_mystylename = str_replace('_css.inc', '', basename(__FILE__));
	if (isset($MYSTYLE[$_mystylename])) {
		disp_mystyle($_mystylename);
	}
}

?>