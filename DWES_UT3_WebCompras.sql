create database comprasweb;
use comprasweb;

create table cliente
(nif varchar(9),
 nombre varchar(40),
 apellido varchar(40),
 cp varchar(5),
 direccion varchar(40),
 ciudad varchar(40));
 
alter table cliente add constraint pk_cliente primary key (nif); 

create table categoria
(id_categoria varchar(5),
 nombre varchar(40));
 
alter table categoria add constraint pk_categoria primary key (id_categoria); 

create table almacen
(num_almacen integer,
 localidad varchar(40));
 
alter table almacen add constraint pk_almacen primary key (num_almacen); 


create table producto
(id_producto varchar(5),
 nombre		varchar(40),
 precio		double,
 id_categoria varchar(5));

alter table producto add constraint pk_producto primary key (id_producto); 

alter table producto add constraint fk_prod_cat foreign key (id_categoria) references categoria(id_categoria); 

create table compra
(nif varchar(9),
 id_producto varchar(5),
 unidades integer);
 
alter table compra add constraint pk_compra primary key (nif,id_producto,fecha_compra);  
 
alter table compra add constraint fk_com_cli foreign key (nif) references cliente(nif);  

alter table compra add constraint fk_com_pro foreign key (id_producto) references producto(id_producto); 

create table almacena
(num_almacen integer,
 id_producto varchar(5),
 cantidad integer);

alter table almacena add constraint pk_almacena primary key (num_almacen,id_producto); 

alter table almacena add constraint fk_alm_alm foreign key (num_almacen) references almacen(num_almacen);  

alter table almacena add constraint fk_alm_pro foreign key (id_producto) references producto(id_producto);

alter table compra ADD fecha_compra DATETIME NOT NULL; 


alter table cliente add password varchar(40) NOT NULL;


SOLO EN CASO NECESARIO
drop table compra;









