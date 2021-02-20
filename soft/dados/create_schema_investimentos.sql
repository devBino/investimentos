create database investimento;

use investimento;

create table usuario(
	cdUsuario bigint not null auto_increment,
    nmUsuario varchar(50) not null unique,
    dsSenha varchar(100) not null unique,
    cdPermissao int not null,
    dtUpdate datetime not null,
    cdUsuarioUpdate int not null,
    
    primary key(cdUsuario)
)default charset = utf8;

INSERT INTO usuario
(nmUsuario,dsSenha,cdPermissao)
VALUES
('admin','d033e22ae348aeb5660fc2140aec35850c4da997',1);

create table papel(
    cdPapel bigint not null auto_increment,
    nmPapel varchar(150) not null,
    cotacao decimal(12,2) not null default 0,
    cdTipo int not null,
    subTipo int not null,
    taxaIr decimal(10,2) not null default 0,
    cdUsuario bigint not null,

    foreign key(cdUsuario) references usuario(cdUsuario),
    primary key(cdPapel)
)default charset = utf8;

create table aportes(

    cdAporte bigint not null auto_increment,
    cdPapel bigint not null,
    valor decimal(12,2) not null,
    qtde int not null,
    subTotal decimal(12,2) not null,
    dtAporte datetime default current_timestamp,
    cdStatus int not null,
    taxaRetorno decimal(12,4) default 0,
    taxaAdmin decimal(12,4) default 0,
    cdUsuario bigint not null,

    foreign key(cdPapel) references papel(cdPapel),
    foreign key(cdUsuario) references usuario(cdUsuario),
    primary key(cdAporte)

)default charset = utf8;

create table resgates(
    cdResgate bigint not null auto_increment,
    cdPapel bigint not null,
    cdAporte bigint not null,
    valor decimal(12,2) not null,
    qtde int not null,
    subTotal decimal(12,2) not null,
    capitalInicial  decimal(12,2) not null,
    diasCorridos  int not null,
    montanteBruto  decimal(12,2) not null,
    montanteLiquido  decimal(12,2) not null,
    lucroBruto  decimal(12,2) not null,
    lucroLiquido  decimal(12,2) not null,
    taxaRetorno decimal(12,4) default 0,
    taxaAdmin  decimal(12,2) not null,
    taxaIof  decimal(12,2) not null,
    taxaIr  decimal(12,2) not null,
    descontoIof  decimal(12,2) not null,
    descontoIr  decimal(12,2) not null,
    descontoAdmin  decimal(12,2) not null,
    dtResgate datetime default current_timestamp,
    cdUsuario bigint not null,

    foreign key(cdPapel) references papel(cdPapel),
    foreign key(cdAporte) references aportes(cdAporte),
    foreign key(cdUsuario) references usuario(cdUsuario),
    primary key(cdResgate)
)default charset = utf8;

create table proventos(
    cdProvento bigint not null auto_increment,
    cdPapel bigint not null,
    valor decimal(12,2) not null,
    qtde int not null,
    subTotal decimal(12,2) not null,
    cdTipo int not null,
    dtProvento datetime default current_timestamp,
    cdUsuario bigint not null,

    foreign key(cdUsuario) references usuario(cdUsuario),
    primary key(cdProvento)
)default charset = utf8;

create table lancamentos(
    cdLancamento bigint not null auto_increment,
    descricao varchar(150) not null,
    valor decimal(12,2) not null,
    dtLancamento datetime default current_timestamp,
    cdTipo int not null,
    cdUsuario bigint not null,
    
    foreign key(cdUsuario) references usuario(cdUsuario),
    primary key(cdLancamento)
)default charset = utf8;

create table informe(
    cdInforme bigint not null auto_increment,
    descricao varchar(150) not null,
    valor decimal(12,2) not null,
    dtInforme date,
    cdUsuario bigint not null,
    
    foreign key(cdUsuario) references usuario(cdUsuario),
    primary key(cdInforme)
)default charset = utf8;

create index idx_informe on informe(dtInforme,cdUsuario,descricao);

create table historicoCotacoes(
    cdHistorico bigint not null auto_increment,
    cdPapel bigint not null,
    cotacao decimal(12,2) not null default 0,
    dtCotacao datetime default current_timestamp,
    cdUsuario bigint not null,

    foreign key(cdPapel) references papel(cdPapel),
    foreign key(cdUsuario) references usuario(cdUsuario),
    primary key(cdHistorico)
)default charset = utf8;