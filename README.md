### developing
=======
# FlatDB (em desenvolvimento)
flatDB é um banco de dados baseado em armazenamento ***multi-level file flat***. Uma ótima solução quando não há nenhum outro banco de dados disponível.
> Sei que é algo improvável, mas na empresa onde trabalho (alias, uma multinacional) não disponibilizam um DB. Já que não faço parte da equipe de DEV (questão burocrática). Na real nem tem uma extensão de acesso a banco de dados como: PDO ou MySQL; por exemplo. Mas como *"brasileiro nao desisti nunca"*, surgiu esse projeto em base nesse carência por DB. 

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


##### estrutura de como é salvo os dados
```
|- _data.flatdb/ (diretorio principal)
    |- example.db/ (diretorio Database)
        |- users.tb/ (diretorio Table)
            |- .cache/ (diretorio Cache)
            |- .metadata.php (arquivo metadados)
            |- 0.63661977236754i1.php (documento)
            |- 0.95492965855131i2.php (documento)
            |- [...]
        |- products.tb/ (diretorio Table)
            |- .cache/ (diretorio Cache)
            |- .metadata.php (arquivo metadados)
            |- 0.63661977236754i1.php (documento)
            |- 0.95492965855131i2.php (documento)
            |- [...]
```


##### Segurança
Caso for usar algo confidencial como ***password***, criptografá-la (criar uma *hash*). Utilize `password_hash()` -[doc](http://php.net/manual/en/function.password-hash.php)-, ao invés de  `md5()` ou `sha1()` (não são funções seguras para *hash*). *Isso é para a vida*
