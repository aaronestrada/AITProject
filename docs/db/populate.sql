INSERT INTO `author` 
(`id`, `name`, `contact`, `email`, `phone`, `created_at`, `modified_at`) 
VALUES 
('1', 'Ufficio Organizzazione', 'http://www.provincia.bz.it/dipartimenti/direzione-generale/', 'organisation@provinz.bz.it', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('2', 'Area funzionale "Turismo"', 'http://www.provincia.bz.it/economia/', 'tourismus@provincia.bz.it', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('3', 'Istituto Provinciale di Statistica ASTAT', 'http://www.provinz.bz.it/astat', 'astat@provincia.bz.it', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('4', 'Ripartizione Finanze', 'http://www.retecivica.bz.it/it/pubblicazioni-istituzionali/atti-di-concessione-feaga-feasr.asp', 'finanze@provincia.bz.it', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('5', 'SASA SpA', '	http://opensasa.info/', 'info@sasabus.org', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `document` 
(`id`, `author_id`, `status`, `name`, `description`, `details_link`, `price`, `created_at`, `modified_at`, `published_at`, `filename`) 
VALUES 
('1', '1', '1', 'Unità organizzative della provincia autonoma di Bolzano', 'Il dataset contiene i dati relativi alle unità organizzative della provincia autonoma di Bolzano, comprendenti l’indirizzo e le informazioni di contatto (numero di telefono, fax, e-mail, PEC).', 'http://dati.retecivica.bz.it/it/dataset/unita-organizzative-della-provincia-autonoma-di-bolzano', '10.99', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2015-08-05 15:19:03', 'dipartimenti.csv'),
('2', '2', '1', 'Associazioni turistiche in Alto Adige', 'Il dataset contiene i contatti delle associazioni turistiche in Alto Adige', 'http://dati.retecivica.bz.it/it/dataset/uffici-turistici-dell-alto-adige', '5.50', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2016-02-05 10:00:03', 'elecnoassociazioni-turistichealto-adige.xls'),
('3', '2', '1', 'Consorzi turistici in Alto Adige', 'Il dataset contiene i contatti dei consorzi turistici in Alto Adige', 'http://dati.retecivica.bz.it/it/dataset/enti-turistici-dell-alto-adige', '6.00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2016-02-05 10:10:03', 'elencoconsorzituristici.xls'),
('4', '3', '1', 'Turismo: conto satellite', 'Il dataset fornisce i dati statistici relativi alla all’offerta e al consumo turistico interno nei comuni della Provincia Autonoma di Bolzano', 'http://dati.retecivica.bz.it/it/dataset/turismo-conto-satellite', '10.00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2009-03-03 14:50:55', 'TSA_CST_2008.xls'),
('5', '3', '1', 'Pensioni', 'Il dataset fornisce i dati statistici relativi al sistema pensionistico nella Provincia Autonoma di Bolzano.', 'http://dati.retecivica.bz.it/it/dataset/pensioni', '22.40', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2016-02-02 11:22:33', 'mit42_2015-tabellen.xls'),
('6', '3', '1', 'Agricoltura e foreste: Allevamenti', 'Il dataset fornisce i dati statistici relativi all’allevamento nella Provincia Autonoma di Bolzano.', 'http://dati.retecivica.bz.it/it/dataset/agricoltura-e-foreste-allevamenti', '6.50', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2016-02-02 12:13:14', 'kapitel_3.xls'),
('7', '3', '1', 'Agricoltura e foreste: informazioni varie', 'Il dataset fornisce varie tipologie di dati statistici relativi a vari ambiti agricoli e forestali della Provincia Autonoma di Bolzano.', 'http://dati.retecivica.bz.it/it/dataset/agricoltura-e-foreste-informazioni-varie', '6.50', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2016-02-02 12:15:50', 'kapitel_5.xls'),
('8', '4', '1', 'Beneficiari di finanziamenti del FEAGA e FEASR', 'Pubblicazione di informazioni sui beneficiari dei finanziamenti provenienti dal Fondo europeo agricolo di garanzia (FEAGA) e dal Fondo europeo agricolo per lo sviluppo rurale (FEASR) ai sensi del Regolamento (CE) n. 259/2008 della Commissione (applicazione del Regolamento (CE) n. 1290/2005 del Consiglio).', 'http://dati.retecivica.bz.it/it/dataset/beneficiari2-di-finanziamenti-del-feaga-e-feasr', '12.00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2016-03-15 10:33:10', 'OPP_2015.csv'),
('9', '4', '1', 'Beneficiari di provvidenze economiche', 'Pubblicazione dei beneficiari e rispettivi contributi e sussidi erogati.', 'http://dati.retecivica.bz.it/it/dataset/beneficiari-di-provvidenze-economiche', '14.00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2016-03-15 10:50:11', 'EXTCON_2R_2015.csv'),
('10', '5', '1', 'Calendario mezzi pubblici SASA', 'Il dataset contiene le informazioni sul calendario e sulle versioni orari. Inoltre serve per conettere le corse di un certo tipo di giorno fra di loro. I dati sono pubblicati sul portale OpenSASA.', 'http://dati.retecivica.bz.it/it/dataset/calendario-sasa', '4.00', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2015-01-08 15:44:01', 'calendario.csv');

INSERT INTO `tag` 
(`id`, `name`) 
VALUES 
('1', 'indirizzo'), 
('2', 'provincia'), 
('3', 'unità amministrative'), 
('4', 'amministrazione'), 
('5', 'autonoma dati'), 
('6', 'telefono'), 
('7', 'associazioni'), 
('8', 'attività turistiche'), 
('9', 'turismo'), 
('10', 'ente'), 
('11', 'comuni'), 
('12', 'statistica'),
('13', 'pensioni'),
('14', 'foresta'),
('15', 'agricoltura'),
('16', 'beneficiari'),
('17', 'provvidenze economiche'),
('18', 'bus'),
('19', 'calendario'),
('20', 'orario'),
('21', 'sasa'),
('22', 'trasporto');    

INSERT INTO `document_tag` 
(`document_id`, `tag_id`) 
VALUES 
('1', '1'), 
('1', '2'), 
('1', '3'), 
('1', '4'), 
('1', '5'), 
('1', '6'), 
('2', '4'), 
('2', '7'), 
('2', '8'), 
('2', '9'), 
('3', '4'), 
('3', '8'), 
('3', '9'), 
('3', '10'), 
('4', '2'), 
('4', '8'), 
('4', '9'), 
('4', '11'), 
('4', '12'), 
('5', '2'), 
('5', '11'), 
('5', '12'), 
('5', '13'), 
('6', '12'), 
('6', '14'), 
('6', '15'), 
('7', '12'), 
('7', '14'), 
('7', '15'), 
('8', '16'), 
('9', '16'), 
('9', '17'), 
('10', '18'), 
('10', '19'), 
('10', '20'), 
('10', '21'), 
('10', '22');

INSERT INTO `user` 
(`id`, `email`, `password`, `firstname`, `lastname`, `birthdate`, `status`, `created_at`, `modified_at`) 
VALUES 
('1', 'john.doe@example.com', 'hashed_password', 'John', 'Doe', '1966-04-15', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), -- many purchases & basket items
('2', 'arthur.wallace@example.com', 'hashed_password', 'Arthur', 'Wallace', '1981-12-05', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), -- 1 purchase, 1 item in basket
('3', 'jamie.collins@example.com', 'hashed_password', 'Jamie', 'Collins', '1950-01-30', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP); -- no purchases, empty basket
 
INSERT INTO `purchase` 
(`id`, `user_id`, `created_at`) 
VALUES 
('1', '1', '2016-04-07 11:21:22'), 
('2', '1', '2016-04-09 15:38:08'), 
('3', '1', '2016-04-14 12:01:11'), 
('4', '2', '2016-04-08 19:33:40');

INSERT INTO `purchase_document` 
(`purchase_id`, `document_id`, `price`) 
VALUES 
('1', '1', '10.99'), 
('1', '2', '5.50'), 
('1', '3', '6.00'), 
('2', '10', '4.00'), 
('2', '9', '14.00'), 
('3', '5', '0.99'), 
('4', '2', '5.50');

INSERT INTO `user_document_cart` 
(`document_id`, `user_id`) 
VALUES 
('10', '1'),
('7', '1'),
('8', '1'),
('9', '1'),
('4', '1'),
('7', '2');
