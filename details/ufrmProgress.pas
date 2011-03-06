(*

Some Error fix in trunk

*)

unit ufrmProgress;

interface

uses
  Windows, Messages, SysUtils, Classes, Graphics, Controls, Forms, Dialogs,
  StdCtrls, Buttons, ComCtrls;

type
  TfrmProgress = class(TForm)
    pbProgress : TProgressBar;
    cmdCancel  : TBitBtn;
    lbInfo     : TLabel;

    procedure cmdCancelClick(Sender: TObject);

  private
    FCancel   : ^Boolean;
    FPosition : Integer;
    FInfo     : String;

    procedure SetInfo(aInfo:String);
    procedure SetPosition(aPosition:Integer);

  public
    constructor CreateProgress(AOwner:TComponent;aTitle,aInfo:String;min,max,
      pos:Integer;var cancel:Boolean);

    property Info:String read FInfo write SetInfo;
    property Position:Integer read FPosition write SetPosition;
  end;

var
  frmProgress: TfrmProgress;

implementation

{$R *.DFM}

(******************************************************************************)
procedure TfrmProgress.cmdCancelClick(Sender: TObject);
(******************************************************************************)
begin
FCancel^:=true;
Close;
end;

(******************************************************************************)
procedure TfrmProgress.SetInfo(aInfo:String);
(******************************************************************************)
begin
if aInfo<>FInfo then
  begin
  FInfo:=aInfo;
  lbInfo.Caption:=FInfo;
  end;
end;

(******************************************************************************)
procedure TfrmProgress.SetPosition(aPosition:Integer);
(******************************************************************************)
begin
if aPosition<>FPosition then
  begin
  FPosition:=aPosition;
  pbProgress.Position:=FPosition;
  end;
end;

(******************************************************************************)
constructor TfrmProgress.CreateProgress(AOwner:TComponent;aTitle,aInfo:String;
  min,max,pos:Integer;var cancel:Boolean);
(******************************************************************************)
begin
inherited Create(AOwner);

Caption:=aTitle;
lbInfo.Caption:=aInfo;

pbProgress.Min:=min;
pbProgress.Max:=max;
pbProgress.Position:=pos;

FCancel:=@cancel;
FCancel^:=false;
end;

end.
