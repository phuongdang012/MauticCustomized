<?php

namespace MauticPlugin\MauticeSMSBundle\Integration\SDK;

class eSMSResponse
{
    private $codeResult;
    private $countRegenerate;
    private $smsId;
    private $code = [
        '100' => 'Yêu cầu gửi thành công',
        '104' => 'Brandname không tồn tại',
        '118' => 'Loại tin nhắn không hợp lệ',
        '119' => 'Không đủ số lượng người nhận',
        '131' => 'Tin nhắn vượt quá số lượng ký tự',
        '132' => 'Không có quyền hạn gửi tin nhắn đến 8755',
        '99'  => 'Lỗi không xác định',
        '177' => 'Brandname không có hướng',
        '159' => 'Request ID vượt quá 20 ký tự',
        '145' => 'Sai template tin nhắn',
    ];

    public function __construct($codeResult, $countRegenerate, $smsId)
    {
        $this->codeResult      = $codeResult;
        $this->countRegenerate = $countRegenerate;
        $this->smsId           = $smsId;
    }

    public function getCodeResult()
    {
        return $this->codeResult;
    }

    public function getCountRegenerate()
    {
        return $this->countRegenerate;
    }

    public function getSmsId()
    {
        return $this->smsId;
    }

    public function translateCodeToError()
    {
        return $this->code[$this->codeResult];
    }
}
