﻿<?php
//
namespace Mdanter\Ecc\SM2;

use Mdanter\Ecc\SM3\SM3Digest;
use Mdanter\Ecc\SM2\Hex2ByteBuf;
use Mdanter\Ecc\EccFactory;


class Cipher
{
    private $ct = 1;
    
    private  $p2;
    private  $sm3keybase;
    private  $sm3c3;
    
    private  $key = array();
    private  $keyOff = 0;
    
  
    private function  Reset()//注意，加密使用无符号的数组转换，以便与硬件相一致
    {
        $this->sm3keybase = new SM3Digest();
        $this->sm3c3 = new SM3Digest();
        
        $p=array();

        $gmp_x = $this->p2->GetX();
        $x=Hex2ByteBuf::ConvertGmp2ByteArray($gmp_x);
        $this->sm3keybase->BlockUpdate($x, 0, sizeof($x));
        $this->sm3c3->BlockUpdate($x, 0, sizeof($x));

        $gmp_y = $this->p2->GetY();
        $y=Hex2ByteBuf::ConvertGmp2ByteArray($gmp_y);
        $this->sm3keybase->BlockUpdate($y, 0, sizeof($y));
        
        $ct = 1;
        $this->NextKey();
    }
    
    private function  NextKey()
    {
        $sm3keycur = new SM3Digest();
        $sm3keycur->setSM3Digest($this->sm3keybase);
        $sm3keycur->Update( ($this->ct >> 24 & 0x00ff));
        $sm3keycur->Update( ($this->ct >> 16 & 0x00ff));
        $sm3keycur->Update( ($this->ct >> 8 & 0x00ff));
        $sm3keycur->Update( ($this->ct & 0x00ff));
        $sm3keycur->DoFinal($this->key, 0);
        $this->keyOff = 0;
        $this->ct++;
    }
        
    public function  Encrypt($data,$len)
    {
        $this->sm3c3->BlockUpdate($data, 0, $len);
        for ($i = 0; $i <$len; $i++)
        {
            if ($this->keyOff == sizeof($this->key))
                $this->NextKey();
            
                $data[$i] ^= $this->key[$this->keyOff++];
        }
        return $data;
    }
    
    public function Init_dec($userD, $c1)
    {
        $this->p2 = $c1->mul($userD);
        $this->Reset();
    }
    
    public function Decrypt($data,$len)
    {
        for ($i = 0; $i < $len; $i++)
        {
            if ($this->keyOff == sizeof($this->key))
                $this->NextKey();
            
            $data[i] ^= $this->key[$this->keyOff++];
        }
        $this->sm3c3->BlockUpdate($data, 0, $len);
        return $data;
    }
    
    public function  Dofinal()
    {
        $c3=array();
        $gmp_p = $this->p2->GetY();
        $p=Hex2ByteBuf::ConvertGmp2ByteArray($gmp_p);
        $this->sm3c3->BlockUpdate($p, 0, sizeof($p));
        $this->sm3c3->DoFinal($c3, 0);
        //$this->Reset();
        return $c3;
    }
}