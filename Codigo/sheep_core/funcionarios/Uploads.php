<?php


class Uploads {

    private $File;
    private $Name;
    private $Send;
    private $Width;
    private $Image;
    private $Result;
    private $Error;
    private $Folder;
    private static $BaseDir;

    function __construct($BaseDir = null) {
        
        //para alterar o nome padrão da pasta de uploads de imagens basta alterar '../uploads/'
        self::$BaseDir = ( (string) $BaseDir ? $BaseDir : '../sheep-imagens/');
        if (!file_exists(self::$BaseDir) && !is_dir(self::$BaseDir)):
            mkdir(self::$BaseDir, 0755);

        endif;
    }

 
    public function Image(array $Image, $Name = null, $Width = null, $Folder = null) {
        $this->File = $Image;
        $this->Name = ( (string) $Name ? $Name : substr($Image['name'], 0, strrpos($Image['name'], '.')) );
        $this->Width = ((int) $Width ? $Width : 2000);
        
        //alterar o nome da criação da pasta automática 'images' para os arquivos
        $this->Folder = ((string) $Folder ? $Folder : 'images');
        
        if(in_array('.php', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos PHP"; 
        }
        
        if(in_array('.phtml', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos phtml"; 
        }
        
        if(in_array('.xhtml', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos phtml"; 
        }
        
        if(in_array('.html', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos html"; 
        }
        
        if(in_array('.sql', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos sql"; 
        }
        
        if(in_array('.js', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos js"; 
        }
        
        if(in_array('.shell', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos shel"; 
        }
        
        if(in_array('.jdk', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos jdk"; 
        }
        
        if(in_array('.json', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos jdk"; 
        }
        
        if(in_array('.htaccess', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos htaccess"; 
        }
        
        if(in_array('.xml', $Image)){
           $this->Result = false;
           $this->Error = "Não aceitamos arquivos xml"; 
        }
        


        $this->CheckFolder($this->Folder);
        $this->setFileName();
        $this->UploadImage();
    }
    
    
  
  
    public function getResult() {
        return $this->Result;
    }

   
    public function getError() {
        return $this->Error;
    }

    
    
    //Verifica e cria os diretórios com base em tipo de arquivo, ano e mês!
    private function CheckFolder($Folder) {
        list($y, $m) = explode('/', date('Y/m'));
        $this->CreateFolder("{$Folder}");
        $this->CreateFolder("{$Folder}/{$y}");
        $this->CreateFolder("{$Folder}/{$y}/{$m}/");
        $this->Send = "{$Folder}/{$y}/{$m}/";
    }

    //Verifica e cria o diretório base!
    private function CreateFolder($Folder) {
        if (!file_exists(self::$BaseDir . $Folder) && !is_dir(self::$BaseDir . $Folder)):
            mkdir(self::$BaseDir . $Folder, 0755);

        endif;
    }

    //Verifica e monta o nome dos arquivos tratando a string!
    private function setFileName() {
        $FileName = Formata::Name($this->Name) . strrchr($this->File['name'], '.');
        if (file_exists(self::$BaseDir . $this->Send . $FileName)):
            $FileName = Formata::Name($this->Name) . '-' . time() . strrchr($this->File['name'], '.');
        endif;
        $this->Name = $FileName;
    }

    //Realiza o upload de imagens redimensionando a mesma!
    public function UploadImage() {
        switch ($this->File['type']):
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $this->Image = imagecreatefromjpeg($this->File['tmp_name']);
                break;

            case 'image/png':
            case 'image/x-png':
                $this->Image = imagecreatefrompng($this->File['tmp_name']);
                break;

            case 'image/gif':
                
                $this->Image = imagecreatefromgif($this->File['tmp_name']);
                
                break;
            
            case 'image/vnd.wap.wbmp': 
                $this->Image = imagecreatefromwbmp($this->File['tmp_name']);
                break;

        endswitch;

        if (!$this->Image):
            $this->Result = false;
            $this->Error = 'Tipo de arquivo inválido, envie imagens JPG, GIF ou PNG!';
        else:
            
            $x = imagesx($this->Image);
            $y = imagesy($this->Image);
            $ImageX = ( $this->Width < $x ? $this->Width : $x );
            $ImageH = ( $ImageX * $y ) / $x;

            $NewImage = imagecreatetruecolor($ImageX, $ImageH);
            imagealphablending($NewImage, false);
            imagesavealpha($NewImage, true);
            imagecopyresampled($NewImage, $this->Image, 0, 0, 0, 0, $ImageX, $ImageH, $x, $y);

            switch ($this->File['type']):
                case 'image/jpg':
                case 'image/jpeg':
                case 'image/pjpeg':
                    imagejpeg($NewImage, self::$BaseDir . $this->Send . $this->Name);
                    break;

                case 'image/png':
                case 'image/x-png':
                    imagepng($NewImage, self::$BaseDir . $this->Send . $this->Name);
                    break;

                case 'image/gif':
                    imagegif($NewImage, self::$BaseDir . $this->Send . $this->Name);
                    break;

            endswitch;

                if(!$NewImage):
                    $this->Result = false;
                    $this->Error = 'Tipo de arquivo inválido, envie imagens JPG, GIF ou PNG!';
                    else:
                        $this->Result = $this->Send . $this->Name;
                        $this->Error = null;
                endif;

                imagedestroy($this->Image);
                imagedestroy($NewImage);
        endif;
    }
    
    //envia arquivos e midias
    private function MoveFile(){
        if(move_uploaded_file($this->File['tmp_name'], self::$BaseDir . $this->Send . $this->Name)):
            $this->Result = $this->Send . $this->Name;
            $this->Error = null;
            else:
                $this->Result = false;
                $this->Error = 'Erro ao mover o arquivo para o servidor. Favor tente mais tarde!';
            
        endif;
    }
}
