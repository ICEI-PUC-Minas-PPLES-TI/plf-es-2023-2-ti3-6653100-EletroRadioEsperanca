<?php

class Atualizar extends Conexao {

    private $Banco;
    private $Dados;
    private $SQL;
    private $Locais;
    private $Resultado;

    
    private $Atualizar;

    
    private $Conexao;

    //FAZ A ATUALIZAÇÃO
    public function Atualizando($Banco, array $Dados, $SQL, $Adicionais) {
        $this->Tabela = (string) $Banco;
        $this->Dados = $Dados;
        $this->Termos = (string) $SQL;
        
        parse_str($Adicionais, $this->Locais);
        $this->getSyntax();
        $this->Execute();
    }

    /** @var Retorna um Resultado de cadastro ou não  */
    public function getResultado() {
        return $this->Resultado;
    }

    /** @var FAZ A CONTAGEM DOS CAMPOS DA TABLEA  */
    public function getContaLinhas() {
        return $this->Atualizar->rowCount();
    }

    /**
     * <b>setLocais</b>
     * SERVE PARA ADICIONAR LIMIT, OFFSET E LINKS DE MANEIRA SIMPLIFICADA
     * @param STRING $Adicionais informe os links, limit e offset do BD exemplo: "name=Oliver&views=5&limit=7"
     * 
     * */
    public function setLocais($Adicionais) {
        parse_str($Adicionais, $this->Locais);
        $this->getSyntax();
        $this->Execute();
    }

    

    /** @var Faz a coneção com banco de dados */
    private function Canectar() {

        $this->Conexao = parent::getCanectar();
        $this->Atualizar = $this->Conexao->prepare($this->Atualizar);
  
    }

    /** @var gera a syntax do mysql automaticamente */
    private function getSyntax() {
        foreach ($this->Dados as $key => $Value):
            $Locais[] = $key .  ' = :' . $key;
        endforeach;
        
        $Locais = implode(', ', $Locais);
        $this->Atualizar = "UPDATE {$this->Tabela} SET {$Locais} {$this->Termos}";
    }

    /** @var Executa o PDO  */
    private function Execute() {
        $this->Canectar();

        try {
            $this->Atualizar->execute(array_merge($this->Dados, $this->Locais));
            $this->Resultado = true;
        } catch (Exception $wt) {
            $this->Resultado = null;
            echo "<b>Erro ao Atulizar: {$wt->getMessage()}</b> - {$wt->getCode()}" ;
        }
    }

}
