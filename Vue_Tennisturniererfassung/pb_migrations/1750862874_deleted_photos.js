/// <reference path="../pb_data/types.d.ts" />
migrate((app) => {
  const collection = app.findCollectionByNameOrId("pbc_1063624087");

  return app.delete(collection);
}, (app) => {
  const collection = new Collection({
    "createRule": "",
    "deleteRule": "",
    "fields": [
      {
        "autogeneratePattern": "[a-z0-9]{15}",
        "hidden": false,
        "id": "text3208210256",
        "max": 15,
        "min": 15,
        "name": "id",
        "pattern": "^[a-z0-9]+$",
        "presentable": false,
        "primaryKey": true,
        "required": true,
        "system": true,
        "type": "text"
      },
      {
        "hidden": false,
        "id": "file347571224",
        "maxSelect": 1,
        "maxSize": 0,
        "mimeTypes": [],
        "name": "photo",
        "presentable": false,
        "protected": false,
        "required": false,
        "system": false,
        "thumbs": [],
        "type": "file"
      },
      {
        "cascadeDelete": false,
        "collectionId": "pbc_340646327",
        "hidden": false,
        "id": "relation869376999",
        "maxSelect": 1,
        "minSelect": 0,
        "name": "tournament_id",
        "presentable": false,
        "required": false,
        "system": false,
        "type": "relation"
      },
      {
        "hidden": false,
        "id": "autodate2990389176",
        "name": "created",
        "onCreate": true,
        "onUpdate": false,
        "presentable": false,
        "system": false,
        "type": "autodate"
      },
      {
        "hidden": false,
        "id": "autodate3332085495",
        "name": "updated",
        "onCreate": true,
        "onUpdate": true,
        "presentable": false,
        "system": false,
        "type": "autodate"
      }
    ],
    "id": "pbc_1063624087",
    "indexes": [],
    "listRule": "",
    "name": "photos",
    "system": false,
    "type": "base",
    "updateRule": "",
    "viewRule": ""
  });

  return app.save(collection);
})
