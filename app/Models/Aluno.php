<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Uspdev\Replicado\Pessoa;

class Aluno extends Model
{
    use HasFactory;

    protected $fillable = ['id','nome','monografia_id'];

    /**
     * Relacionamento 1:N
     */
    public function monografias() {
        return $this->hasMany(Monografia::class);
    }

    /**
     * Usado para excluir alunos em lote através do id da Monografia
     * @param id Id da Monografia
     */
    static function excluirRegistroByMonografiaId($id) {
        return DB::table('alunos')->where('monografia_id', $id)->delete();
    }

    /**
     * Método para pegar os dados de Alunos na tabela do replicado
     * @param  codpes int [OPCIONAL] Número USP do Aluno
     * @return var:Array Retorna array de objetos 
     */
    static function getDadosAluno(int $codpes = 0) {
        //Buscar no banco de dados replicado
        //return Pessoa::dump($codpes);

        $replicado = new Replicado;
        
        return $replicado->getDadosPessoas($codpes,"ALUNOGR");
        
    }
}
