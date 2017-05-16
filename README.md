
# FlatDB
flatDB é um banco de dados baseado em armazenamento ***multi-level file flat***. Uma ótima solução quando não há nenhum outro banco de dados disponível.


**Destaques:**
- Sistemas baseados em armazenamento chave-valor
- Acessar elemento via *dot notation*
- Livre de esquema
- Sistema de armazenamento em cache integrado, para melhorar o desempenho
- Métodos de encadeamento
- Operação CRUD (create, read, update, delete)
- suporte a:  WHERE(), LIMIT(), OFFSET(), ORDER(), LENGTH() entre outros
- Suporte multi banco de dados e tabelas
- Protegido contra  *access from web-browser* por URL


**Estrutura de como é salvo os dados:**

```
|- _data.flatdb/ (diretorio principal)
    |- example.db/ (diretorio Database)
        |- users.tb/ (diretorio Table)
            |- .cache/ (diretorio Cache)
            |- .metadata.php (arquivo metadados)
            |- 1b945f59.php (documento)
            |- 1c845f70.php (documento)
            |- [...]
        |- products.tb/ (diretorio Table)
            |- .cache/ (diretorio Cache)
            |- .metadata.php (arquivo metadados)
            |- 5d4b7cae.php (documento)
            |- 5d25472c.php (documento)
            |- [...]
    |- example2.db/(diretorio Database)
        |- sunday.tb/ (diretorio Table)
            |- [...]
```

## Instalação

Se ja possui o arquivo [`composer.json`](https://getcomposer.org/), basta adicionar a seguinte dependência ao seu projeto:
```json
{
    "require": {
        "darkziul/flatdb": "1.*"
    }
}
```

Com a dependência adicionada, basta executar:

```
composer install
```

Alternativamente, você pode executar diretamente em seu terminal:

```
composer require "darkziul/flatdb"
```

## Iniciando

FlatDB usa-se **Encadeamento de métodos**, exemplo:

`$fdb->db('test')->table('product')->select('name')->where(['type' => 'tv'])->execute();`


Sintaxe a ser seguida: **(instância)->(banco de dados)->(tabela)->(ação)->(executar/gerar)**
| $instance->db->table->...->execute()


* modo errado: **(instância)->(tabela)->(ação)->(executar/gerar)** 

>  Fatal Error: Nao ha database para consulta!

* modo errado: **(instância)->(banco de dados)->(ação)->(executar/gerar)** 

> Fatal Error: Nao ha tabela para consulta!




## Instância:

#### new FlatDB($dirInit [, $create = false])
| $fdb = new FlatDB($dirInit, $create);

* **$dirInit** (string): precisa começar sem e terminar com `/`. Exemplo: `data/`. Se não for declarada nenhum valor, será considerado o caminho padrão da `class FlatDB`.
* **$create** `true` caso o diretório não exista, força sua criação. `false` é o padrão.

```php
$flatdb = new FlatDB();
// igual à $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/_data.flatdb/', algo como www/_data.flatdb/
```
```php

$flatdb = new FlatDB('data/', true); 
//Caso não exista... será criado, nesse exemplo será criado no diretorio que está sendo executado o código

$flatdb = new FlatDB('data2/');
//caso não exista emite um erro fatal
```



### `db($name [, $create = false])`
| $flatdb->db($dbname)

Selecionar o banco de dados para consulta.

* **$name** Nome do Banco
* **$create** `true` Caso o banco de dados $name não existir será criado. `false` é o padrão

**Seus derivados**
```php
    $flatdb->dbExists('example'); // bool - saber se existe o db
    $flatdb->dbCreate('example'); // bool - criar o db
    $flatdb->dbDelete('example'); // bool - deletar o db

    $flatdb->db('example'); // FatalError | this - selecionar o db
    $flatdb->db('example', true); // this - selecionar; caso nao exista é criado
```

### `table($name [, $create = false])`
| $flatdb->db($dbname)->$table($tbname)

Selecionar a tabela para consulta.

* **$name** Nome da tabela
* **$create** `true` Caso a tabela $name não existir, será criado. `false` é o padrão

**Seus derivados**
```php
    $flatdb->db('example')->tableShow($json = false); // array|string - retorna todos os nomes das tabelas
    //return 
    //[0  => 'default.tb', 'length' =>  1]

    $flatdb->db('example')->tableExists('default'); //bool - Saber se existe a tabela
    $flatdb->db('example')->tableDelete('default'); //bool - Deletar a Tabela
    $flatdb->db('example')->tableCreate('default'); //bool - Criar a tabela

    $flatdb->db('example')->table('default'); // FatalError | this - Selecionar a tabela, caso exista
    $flatdb->db('example')->table('default', true); //this - Selecionar a tabela, caso não exista será criada
```

### `insert($array)`
| $flatdb->db($dbname)->table($tbname)->insert($array)->execute();

Adicionar um novo documento

* **$array** Array a ser adicionada no novo documento

```php
    $whoArr = [' PARENT ', 'Self', 'OthEr', ' ChilD    '];
    $arrInsert = [
        'who'=> $whoArr[mt_rand(0, count($whoArr)-1)],
        'uniqid'=> uniqid(rand(),true),
        ' NUMBER '=>rand(0,90),
        'GrouP.a     '=> substr(uniqid(rand(),true), -10),
        'group.b'=> substr(uniqid(rand(),true), -10),
        'group.c'=> substr(uniqid(rand(),true), -10),
        'unid' => 15,
        'collection.item.group' => ['TEST' => [51, 2, 5, ' GnulId' => 999]],
        'collection.item.id' => password_hash(uniqid(rand(),true), PASSWORD_DEFAULT)
    ]; 
    $flatdb->db('example')->table('default')->insert($arrInsert)->execute(); // array | null
    //return 
```

### `delete($id)`
| $flatdb->db($dbname)->table($tbname)->delete(array|int)->execute();
| $flatdb->db($dbname)->table($tbname)->delete(array|int)->where($condition)->execute();

Deleta o $id(s), retorna o valor de quantos $id foram deletados

* **$id**(array|int) Id ou grupo de ids que serão deletados.

```php
    $flatdb->db('example')->table('default')->delete(15)->execute(); // int | fatalError - Deleta o Id mencionado
    $flatdb->db('example')->table('default')->delete([5, 10, 9])->execute(); // int | fatalError - Deleta os Ids mencionados

    //deeltar todos os documentos que tiverem os valores de where() ===
    $flatdb->db('example')->table('default')->delete()->where(['who' => 'parent', 'number' => 5])->execute(); // int - returna a quantidade deletada

    //deletar o $id caso tenha os valores de where() ===
    $flatdb->db('example')->table('default')->delete(8)->where(['who' => 'parent', 'number' => 5])->execute(); // int - returna a quantidade deletada
```

### `update($key, $value)` ou `update($array)`
| $flatdb->db($dbname)->table($tbname)->update($key, $value)->execute();
| $flatdb->db($dbname)->table($tbname)->update($key, $value)->where($condition)->execute(); [atualizar usando condição]

Atualizar $key com o valor $value

* **$key** Chave que receberá o novo valor.
* **$value** O novo valor
* **$array** Modo alternativo para atualizar vários $key. Grupo de: chave => NovoValor

```php
    //irá atualizar a chave 'who' de todos os documentos
    $flatdb->db('example')->table('default')->update(['who' => 'Xchild'])->execute(); //bool - atualizar grupado
    $flatdb->db('example')->table('default')->update('who', 'Xchild')->execute();// bool - atualizar simples


    //usando where ====
    // irá atualizar 'who' apenas dos documetos que tiverem os valores de where() ===
    $flatdb->db('example')->table('default')->update('who', 'Xchild')->where(['id'=>[5,16]])->execute(); // bool - Atualizar 1 
    $flatdb->db('example')->table('default')->update(['who' => 'Xchild', 'group.b' => 154])->where(['id'=>[5,16]])->execute(); //bool - atualizar vários $key
```



** [escrevendo ....] **





#### Changelog
```
v1.5.0
v1.0.0 - lançamento
```


#### Segurança
Caso for usar algo confidencial como ***password***, precisa-se criptografá-la (*hash*). Utilize `password_hash()` -[doc](http://php.net/manual/en/function.password-hash.php)-, ao invés de  `md5()` ou `sha1()` (não são funções seguras para *hash*). *Isso é para a vida*


